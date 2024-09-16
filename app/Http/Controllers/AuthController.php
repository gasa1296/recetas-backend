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

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $instance = User::where(column: 'email', operator: request()->email)->first();
        if (empty($instance)) {
            $magentoToken = (new MagentoController)->getToken(request: $request);
            if ($magentoToken->getStatusCode() < 300) {
                return response()->json(data: [
                    'recetasUser' => false,
                    'magentoEmail' => $request->email
                ]);
            }
            return response()->json(data: [['email' => __(key: 'email incorrecto')]], status: 404);
        }
        $okResponse = [
            'token' => $instance->createToken(name: 'recipe')->plainTextToken,
            'user' => $instance,
        ];
        if (Hash::check(value: request()->password, hashedValue: $instance->password)) {
            return response()->json(data: $okResponse);
        }
        $magentoToken = (new MagentoController)->getToken(request: $request);
        if ($magentoToken->getStatusCode() < 300) {
            return response()->json(data: $okResponse);
        }
        return response()->json(data: [['password' => __(key: 'contraseña incorrecta')]], status: 404);
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
            return response()->json(data: $validator->errors(), status: 400);
        }
        $inputs = $validator->safe()->all();
        $magento = new MagentoController();
        if (!$magento->verifyFESA(fesa: $inputs['fesa'])) {
            return response()->json(data: ['fesa' => 'Codigo de FESA invalido'], status: 400);
        }
        if (!empty($inputs['idCX']) && empty($inputs['clienteEcommerce'])) {
            $res = $magento->store(inputs: $inputs);
            Log::debug(message: 'magento register', context: $res->getData(assoc: true));
            if ($res->getStatusCode() >= 300) {
                return $res;
            }
        } elseif (empty($inputs['idCX']) && empty($inputs['clienteEcommerce'])) {
            $res = $magento->CX(inputs: $inputs);
            Log::debug(message: 'cx register', context: $res->getData(assoc: true));
            if ($res->getStatusCode() >= 300) {
                return $res;
            }
            $inputs['idCX'] = $res->getData(assoc: true)['idCX'];
            $res = $magento->store(inputs: $inputs);
            Log::debug(message: 'magento register', context: $res->getData(assoc: true));
            if ($res->getStatusCode() >= 300) {
                return $res;
            }
        }
        if (empty($inputs['password'])) {
            $inputs['password'] = Hash::make(value: uuid_create(UUID_TYPE_RANDOM));
        }
        $inputs['fesa'] = !empty($inputs['idCX']) ? $inputs['idCX'] : $inputs['fesa'];
        $instance = User::create(attributes: $inputs);

        event(args: new Registered(user: $instance));
        foreach ($inputs['rooms'] as $key => $el) {
            if (!empty($request->file(key: 'logo_room')[$key])) {
                $file = $request->file(key: 'logo_room')[$key]->store(path: 'medics/' . $instance->id, options: 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            ConsultingRoom::create(attributes: $el);
        }
        foreach ($inputs['specializations'] as $key => $el) {
            if (!empty($request->file(key: 'logo_spec')[$key])) {
                $file = $request->file(key: 'logo_spec')[$key]->store(path: 'medics/' . $instance->id, options: 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            Specialization::create(attributes: $el);
        }

        return response()->json();
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json(data: $request->user());
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make(data: $request->all(), rules: [
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
            return response()->json(data: $validator->errors(), status: 400);
        }
        $instance = auth()->user();
        $inputs = $validator->safe()->all();
        if (!empty($inputs['password'])) {
            $inputs['password'] = Hash::make(value: $inputs['password']);
        }
        $instance->update(attributes: $inputs);
        $inputs = $instance->toArray();
        $inputs['idCX'] = $instance->fesa;
        $inputs['specializations'] = $instance->specializations->toArray();
        $inputs['rooms'] = $instance->rooms->toArray();

        $magento = new MagentoController();
        $magento->CX(inputs: $inputs);
        $magento->update(inputs: $inputs);
        
        return response()->json(data: $instance);
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
