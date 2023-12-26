<?php

namespace App\Http\Controllers;

use App\Models\ConsultingRoom;
use App\Models\Specialization;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $instance = User::where('email', request()->email)->first();
        if (empty($instance)) {
            return response()->json([['email' => __('email incorrecto')]], 404);
        }
        if (Hash::check(request()->password, $instance->password)) {
            return response()->json([
                'token' => $instance->createToken('recipe')->plainTextToken,
                'user' => $instance,
            ]);
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'phone1' => ['required', 'string'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['required', 'string'],
            'fesa' => ['required',],
            'rooms' => ['required', 'array'],
            'specializations' => ['required', 'array'],
            'rooms.*.name' => ['required', 'string'],
            'rooms.*.zip' => ['required', 'string'],
            'rooms.*.street' => ['required', 'string'],
            'rooms.*.colony' => ['required', 'string'],
            'rooms.*.state' => ['required', 'string'],
            'rooms.*.delegation' => ['required', 'string'],
            'rooms.*.n_exterior' => ['required',],
            'rooms.*.n_interior' => ['nullable',],
            'rooms.*.address' => ['nullable', 'string'],
            'rooms.*.phone' => ['nullable', 'string'],
            'rooms.*.design' => ['nullable', 'numeric'],
            'specializations.*.name' => ['required', 'string'],
            'specializations.*.identification' => 'required',
            'specializations.*.university' => ['nullable', 'string'],
            'logo_room' => ['required', 'array'],
            'logo_spec' => ['required', 'array'],
            'logo_room.*' => ['required', 'file'],
            'logo_spec.*' => ['required', 'file'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $instance = User::create($inputs);
        event(new Registered($instance));
        foreach ($inputs['rooms'] as $key => $el) {
            if ($request->file('logo_room') && $request->file('logo_room')[$key]) {
                $file = $request->file('logo_room')[$key]->store('medics/' . $instance->id, 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            ConsultingRoom::create($el);
        }
        foreach ($inputs['specializations'] as $key => $el) {
            if ($request->file('logo_spec') && $request->file('logo_spec')[$key]) {
                $file = $request->file('logo_spec')[$key]->store('medics/' . $instance->id, 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            Specialization::create($el);
        }

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
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['nullable', 'string'],
            'phone1' => ['required', 'string'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['required', 'string'],
            'fesa' => ['required',],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $instance = auth()->user();
        $inputs = $validator->safe()->all();
        if(!empty($inputs['password'])) {
            $inputs['password'] = Hash::make($inputs['password']);
        }
        $instance->update($inputs);
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
