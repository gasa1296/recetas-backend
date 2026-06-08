<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $user->load(['rooms', 'specialties']);

        return $this->success(
            __('messages.operation_success'),
            new ProfileResource($user),
        );
    }

    public function update(ProfileRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $user = auth()->user();

        $user->update($inputs);

        $user->load(['rooms', 'specialties']);

        return $this->success(
            __('messages.operation_success'),
            new ProfileResource($user),
        );
    }

    public function destroy(): JsonResponse
    {
        $user = auth()->user();
        $user->delete();

        return $this->success(
            __('messages.operation_success'),
            new ProfileResource($user),
        );
    }
}
