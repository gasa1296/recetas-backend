<?php

namespace App\Http\Controllers;

use App\Models\ConsultingRoom;
use App\Models\Specialization;
use Illuminate\Support\Facades\Storage;
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
     * @todo Add validations
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name1' => 'required',
            'last_name2' => 'required',
            'email' => ['required', 'email', 'unique:users'],
            'password' => 'required',
            'phone1' => 'required',
            'phone2' => '',
            'gender' => 'required',
            'fesa' => 'required',
            'rooms' => ['required', 'array'],
            'specializations' => ['required', 'array'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $instance = User::create($inputs);
        event(new Registered($instance));
        foreach ($inputs['rooms'] as $key => $el) {
            if ($request->file('logo_room') && $request->file('logo_room')[$key]) {
                $file = $request->file('logo_room')[$key]->store('medics/' . $instance->id);
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            ConsultingRoom::create($el);
        }
        foreach ($inputs['specializations'] as $key => $el) {
            if ($request->file('logo_spec') && $request->file('logo_spec')[$key]) {
                $file = $request->file('logo_spec')[$key]->store('medics/' . $instance->id);
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
     * @todo Add validations
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name1' => 'required',
            'last_name2' => 'required',
            'email' => ['required', 'email'],
            'phone1' => 'required',
            'phone2' => '',
            'gender' => 'required',
            'fesa' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $instance = auth()->user();
        $instance->update($validator->safe()->all());
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
