<?php

namespace App\Http\Controllers;

use App\Models\ConsultingRoom;
use App\Models\Specialization;
use Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\{SignupMail, RegisterCompletedMail};

class AuthController extends Controller
{
    public function requestSignup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:users'],
            'name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'professional_id' => ['required', 'string'],
            'specialization' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        Mail::to(env('MAIL_SIGNUP_REPLY_TO'))->send(new SignupMail($inputs));
        return response()->json(['message' => 'Solicitud de registro enviada correctamente']);
    }
    public function login(Request $request): JsonResponse
    {
        $instance = User::where('email', $request->email)->first();
        if (empty($instance)) {
            $magentoToken = (new MagentoController)->getToken($request);
            if ($magentoToken->getStatusCode() < 300) {
                return response()->json([
                    'recetasUser' => false,
                    'magentoEmail' => $request->email
                ]);
            }
            return response()->json([['email' => __('email incorrecto')]], 404);
        }
        $okResponse = [
            'token' => $instance->createToken('recipe')->plainTextToken,
            'user' => $instance,
        ];
        if (Hash::check($request->password, $instance->password)) {
            return response()->json($okResponse);
        }
        $magentoToken = (new MagentoController)->getToken($request);
        if ($magentoToken->getStatusCode() < 300) {
            return response()->json($okResponse);
        }
        return response()->json([['password' => __('contraseña incorrecta')]], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name1' => ['required', 'string'],
            'last_name2' => ['nullable', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['nullable', 'string'],
            'phone1' => ['nullable', 'json'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'fesa' => ['required',],
            'rooms' => ['required', 'array'],
            'specializations' => ['required', 'array'],
            'rooms.*.id_ext' => ['nullable'],
            'rooms.*.name' => ['nullable', 'string'],
            'rooms.*.zip' => ['required', 'string'],
            'rooms.*.street' => ['required', 'string'],
            'rooms.*.colony' => ['required', 'string'],
            'rooms.*.state' => ['required', 'string'],
            'rooms.*.delegation' => ['required', 'string'],
            'rooms.*.n_exterior' => ['required',],
            'rooms.*.n_interior' => ['nullable',],
            'rooms.*.address' => ['nullable', 'string'],
            'rooms.*.phone' => ['nullable', 'string'],
            'rooms.*.fav' => ['nullable'],
            'rooms.*.auto_email' => ['nullable'],
            'rooms.*.auto_whatsapp' => ['nullable'],
            'rooms.*.design' => ['nullable', 'string'],
            'specializations.*.name' => ['required', 'string'],
            'specializations.*.id_ext' => ['nullable'],
            'specializations.*.identification' => ['required', 'unique:specializations'],
            'specializations.*.university' => ['nullable', 'string'],
            'specializations.*.logo' => ['nullable', 'string'],
            'logo_room' => ['nullable', 'array'],
            'logo_spec' => ['nullable', 'array'],
            'logo_room.*' => ['nullable', 'file', 'mimes:jpg,png'],
            'logo_spec.*' => ['nullable', 'file', 'mimes:jpg,png'],

            'idCX' => ['nullable'],
            'clienteEcommerce' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $magento = new MagentoController();

        // Check if the user already exists in Magento
        if (!$magento->verifyFESA($inputs['fesa'])) {
            return response()->json(['fesa' => 'Codigo de FESA invalido'], 400);
        }

        // Create/update the user in CX and Magento
        if(empty($inputs['idCX'])) {
            $res = $magento->CX($inputs);
            if ($res->getStatusCode() >= 300) {
                return $res;
            }
            $inputs['idCX'] = $res->getData(true)['idCX'];
        }
        if (empty($inputs['clienteEcommerce'])) {
            $res = $magento->store($inputs);
            Log::debug('magento register', $res->getData(true));
            if ($res->getStatusCode() >= 300) {
                return $res;
            }
        }
        if (empty($inputs['password'])) {
            $inputs['password'] = Hash::make(uuid_create(UUID_TYPE_RANDOM));
        }
        $inputs['fesa'] = !empty($inputs['idCX']) ? $inputs['idCX'] : $inputs['fesa'];
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
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name1' => ['required', 'string'],
            'last_name2' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'password' => ['nullable', 'string'],
            'phone1' => ['nullable', 'json'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'fesa' => ['required',],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $instance = auth()->user();
        $inputs = $validator->safe()->all();
        if (!empty($inputs['password'])) {
            $inputs['password'] = Hash::make($inputs['password']);
        }
        $instance->update($inputs);
        $inputs = $instance->toArray();
        $inputs['idCX'] = $instance->fesa;
        $inputs['specializations'] = $instance->specializations->toArray();
        $inputs['rooms'] = $instance->rooms->toArray();

        $magento = new MagentoController();
        $magento->CX($inputs);
        $magento->update($inputs);
        
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
