<?php

namespace App\Http\Controllers;

use App\Models\ConsultingRoom;
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
        $instance = User::where('email', request()->email)->firstOrFail();
        if (Hash::check(request()->password, $instance->password)) {
            return response()->json([
                'token' => $instance->createToken('recipe')->plainTextToken,
                'user' => $instance,
            ]);
        }
        return response()->json([], 404);
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
            'email' => 'required|email',
            'password' => 'required',
            'phone1' => 'required',
            'phone2' => '',
            'gender' => 'required',
            'fesa' => 'required',
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $request->all();

        if ($request->file('image')) {
            $inputs['image'] = $request->file('image')->store('avatars');
        }
        if ($request->file('file')) {
            $inputs['logo'] = $request->file('logo')->store('logos');
        }

        $instance = User::create($inputs);
        event(new Registered($instance));
        foreach ($inputs['rooms'] as $room)
        {
            $room['user_id'] = $instance->id;
            ConsultingRoom::create($room);
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
            'email' => 'required|email',
            'phone1' => 'required',
            'phone2' => '',
            'gender' => 'required',
            'fesa' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $instance = auth()->user();
        $inputs = $request->all();
        if ($request->file('image')) {
            $inputs['image'] = $request->file('image')->store('avatars');
            if ($inputs['image']) {
                Storage::delete($instance->image);
            }
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
