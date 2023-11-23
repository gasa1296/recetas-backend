<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            return response()->json([
                'token' => $user->createToken('MyApp')->plainTextToken,
                'user' => $user,
            ]);
        } else {
            return response()->json([], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     * @todo add validations
     * @todo upload file
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name1' => 'required',
            'last_name2' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }
        $user = User::create($request->all());
        $user->sendEmailVerificationNotification();
        event(new Registered($user));
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
     * @todo add validations
     * @todo upload file
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name1' => 'required',
            'last_name2' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = $request->user();
        $user->save();

        return response()->json($user);
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
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->delete();
        return response()->json();
    }
}
