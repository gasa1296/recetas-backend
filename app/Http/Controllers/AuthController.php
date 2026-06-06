<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\StoreRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Models\ConsultingRoom;
use App\Models\Specialization;
use App\Models\User;
use App\Repositories\Interfaces\CXrepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Hash,
};

class AuthController extends Controller
{
    private $CXRepository;

    public function __construct(CXrepositoryInterface $CXRepository)
    {
        $this->CXRepository = $CXRepository;
    }

    public function login(SignInRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        if (! auth()->attempt($inputs)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }
        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(StoreRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $user = User::create($inputs);

        event(new Registered($user));
        $rooms = $inputs['rooms'] ?? [];
        foreach ($rooms as $key => $el) {
            if (! empty($request->file('logo_room')[$key])) {
                $file = $request->file('logo_room')[$key]->store('medics/'.$user->id, 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $user->id;
            ConsultingRoom::create($el);
        }
        foreach ($inputs['specializations'] as $key => $el) {
            if (! empty($request->file('logo_spec')[$key])) {
                $file = $request->file('logo_spec')[$key]->store('medics/'.$user->id, 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $user->id;
            Specialization::create($el);
        }
        //Mail::to($inputs['email'])->send(new RegisterCompletedMail($inputs));

        return response()->json([
            'message' => 'Usuario registrado correctamente',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return response()->json(auth()->user());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $user = auth()->user();
        $inputs = $request->validated();
        if (! empty($inputs['password'])) {
            $inputs['password'] = Hash::make($inputs['password']);
        }
        $user->update($inputs);
        $user = $user->fresh();

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): JsonResponse
    {
        $user = auth()->user();
        $user->delete();

        return response()->json();
    }
}
