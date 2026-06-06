<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultingRoomRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ConsultingRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $rooms = $user->rooms()->paginate(10);

        return response()->json($rooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConsultingRoomRequest $request): JsonResponse
    {
        $user = auth()->user();

        $inputs = $request->validated();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('medics/'.auth()->id(), 'public');
        }
        $room = $user->rooms()->create($inputs);

        return response()->json($room);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $room): JsonResponse
    {
        $user = auth()->user();
        $room = $user->rooms()->findOrFail($room);

        return response()->json($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ConsultingRoomRequest $request, int $room): JsonResponse
    {
        $user = auth()->user();
        $room = $user->rooms()->findOrFail($room);

        $inputs = $request->validated();
        if ($request->file('logo')) {
            if (! empty($room->logo)) {
                Storage::delete($room->logo);
            }
            $inputs['logo'] = $request->file('logo')->store('medics/'.auth()->id(), 'public');
        }
        $room->update($inputs);

        return response()->json($room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $room): JsonResponse
    {
        $user = auth()->user();
        $room = $user->rooms()->findOrFail($room);
        if (! empty($room->logo)) {
            Storage::delete($room->logo);
        }
        $room->delete();

        return response()->json();
    }
}
