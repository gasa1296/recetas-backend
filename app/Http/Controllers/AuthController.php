<?php

namespace App\Http\Controllers;

use App\Models\ConsultingRoom;
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
            $instance = Auth::user();

            return response()->json([
                'token' => $instance->createToken('MyApp')->plainTextToken,
                'user' => $instance,
            ]);
        } else {
            return response()->json([], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     * @todo upload file
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
            'identification' => 'required',
            'especialization' => 'required',
            'phone1' => 'required',
            'phone2' => '',
            'genry' => 'required',
            'university' => 'required',
            'fesa' => 'required',
            'image' => 'required',
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $request->all();
        $instance = User::create($inputs);
        $instance->sendEmailVerificationNotification();
        event(new Registered($instance));
        $inputs['user'] = $instance->id;
        ConsultingRoom::create($inputs);

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
     * @todo upload file
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name1' => 'required',
            'last_name2' => 'required',
            'email' => 'required|email',
            'identification' => 'required',
            'especialization' => 'required',
            'phone1' => 'required',
            'phone2' => '',
            'genry' => 'required',
            'university' => 'required',
            'fesa' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $instance = $request->user();
        $instance->update($request->all());
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
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->delete();
        return response()->json();
    }
}
