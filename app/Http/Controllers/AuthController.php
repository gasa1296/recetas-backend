<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    JsonResponse
};
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\{
    Hash,
    Mail
};
use App\Models\{
    User,
    ConsultingRoom,
    Specialization
};
use App\Repositories\Interfaces\CXrepositoryInterface;
use App\Mail\{
    SignupMail,
    RegisterCompletedMail
};
use App\Http\Requests\Auth\{
    StoreRequest,
    SignUpRequest,
    SignInRequest,
    UpdateRequest
};

class AuthController extends Controller
{
    private $CXRepository;

    public function __construct(CXrepositoryInterface $CXRepository)
    {
        $this->CXRepository = $CXRepository;
    }
    /**
     * Env a solicitud de registro al administrador, con los datos del profesional
     * que se registra.
     *
     * @param SignUpRequest $request
     * @return JsonResponse
     */
    public function requestSignup(SignUpRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        Mail::to(env('MAIL_SIGNUP_REPLY_TO'))->send(new SignupMail($inputs));
        return response()->json(['message' => 'Solicitud de registro enviada correctamente']);
    }
    public function login(SignInRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $instance = User::where('email', $inputs['email'])->first();
        if (empty($instance)) {
            $magentoToken = $this->CXRepository->getToken($inputs);
            if ($magentoToken->getStatusCode() < 300) {
                return response()->json([
                    'recetasUser' => false,
                    'magentoEmail' => $inputs['email']
                ]);
            }
            return response()->json([['email' => __('email incorrecto')]], 404);
        }
        $okResponse = [
            'token' => $instance->createToken('recipe')->plainTextToken,
            'user' => $instance,
        ];
        if (Hash::check($inputs['password'], $instance->password)) {
            return response()->json($okResponse);
        }
        $magentoToken = $this->CXRepository->getToken($inputs);
        if ($magentoToken->getStatusCode() < 300) {
            return response()->json($okResponse);
        }
        return response()->json([['password' => __('contraseña incorrecta')]], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(StoreRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        // Check if the user already exists in Magento
        if($inputs['fesa'] != 0) {
            if (!$this->CXRepository->verifyFESA($inputs['fesa'])) {
                return response()->json(['fesa' => 'Codigo de FESA invalido'], 400);
            }
        }
        $fesa = $inputs['fesa'];
        // Create/update the user in CX and Magento
        if(empty($inputs['idCX'])) {
            $res = $this->CXRepository->CX($inputs);
            if ($res->getStatusCode() >= 300) {
                return $res;
            }
            $inputs['idCX'] = $res->getData(true)['idCX'];
        }
        if (empty($inputs['clienteEcommerce'])) {
            $res = $this->CXRepository->magentoStore($inputs);
            if ($res->getStatusCode() >= 300) {
                return $res;
            }
        }
        if (empty($inputs['password'])) {
            $inputs['password'] = Hash::make(uuid_create(UUID_TYPE_RANDOM));
        }

        $this->CXRepository->verifyAffiliation($inputs['idCX']);
        $this->CXRepository->medicAffiliation($inputs);
        $this->CXRepository->registerFesaCode($inputs);

        $inputs['fesa'] = $inputs['idCX'];
        $instance = User::create($inputs);

        event(new Registered($instance));
        foreach ($inputs['rooms'] as $key => $el) {
            if (!empty($request->file('logo_room')[$key])) {
                $file = $request->file('logo_room')[$key]->store('medics/' . $instance->id, 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            ConsultingRoom::create($el);
        }
        foreach ($inputs['specializations'] as $key => $el) {
            if (!empty($request->file('logo_spec')[$key])) {
                $file = $request->file('logo_spec')[$key]->store('medics/' . $instance->id, 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            Specialization::create($el);
        }
       //Mail::to($inputs['email'])->send(new RegisterCompletedMail($inputs));

        return response()->json();
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $instance = auth()->user();
        $inputs = $request->validated();
        if (!empty($inputs['password'])) {
            $inputs['password'] = Hash::make($inputs['password']);
        }
        $instance->update($inputs);
        $inputs = $instance->toArray();
        $inputs['idCX'] = $instance->fesa;
        $inputs['specializations'] = $instance->specializations->toArray();
        $inputs['rooms'] = $instance->rooms->toArray();

        $this->CXRepository->CX($inputs);
        $this->CXRepository->magentoUpdate($inputs);

        return response()->json($instance);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json();
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): JsonResponse
    {
        auth()->user()->delete();
        return response()->json();
    }
}
