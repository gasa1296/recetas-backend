<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $rooms = auth()->user()->rooms()->paginate(10);

        return $this->success(data: RoomResource::collection($rooms));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomRequest $request): JsonResponse
    {
        $room = auth()->user()->rooms()->create($request->validated());

        return $this->success(data: new RoomResource($room));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $room): JsonResponse
    {
        $room = auth()->user()->rooms()->findOrFail($room);

        return $this->success(data: new RoomResource($room));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomRequest $request, int $room): JsonResponse
    {
        $room = auth()->user()->rooms()->findOrFail($room);
        $room->update($request->validated());

        return $this->success(data: new RoomResource($room));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $room): JsonResponse
    {
        $room = auth()->user()->rooms()->findOrFail($room);
        $room->delete();

        return $this->success();
    }
}
