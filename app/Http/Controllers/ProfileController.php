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
        $user->load(['rooms', 'specialty']);

        return $this->success(
            __('messages.operation_success'),
            new ProfileResource($user),
        );
    }

    public function update(ProfileRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $user = auth()->user();

        $specialtyData = $inputs['specialty'] ?? null;
        unset($inputs['specialty']);

        $user->update($inputs);

        if ($specialtyData) {
            if ($user->specialty) {
                $user->specialty->update($specialtyData);
            } else {
                $user->specialty()->create($specialtyData);
            }
        }

        $user->load(['rooms', 'specialty']);

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
