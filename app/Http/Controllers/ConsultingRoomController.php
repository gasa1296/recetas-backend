<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultingRoom;
use Illuminate\Http\JsonResponse;

class ConsultingRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $instances = ConsultingRoom::where("user_id", auth()->id());
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function store(Request $request): JsonResponse
    {
        $inputs = $request->all();
        $inputs["user_id"] = auth()->id();
        $instance = ConsultingRoom::create($inputs);
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsultingRoom $room): JsonResponse
    {
        if ($room->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        return response()->json($room);
    }

    /**
     * Update the specified resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function update(Request $request, ConsultingRoom $room): JsonResponse
    {
        if ($room->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $room->update($request->all());
        return response()->json($room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsultingRoom $room): JsonResponse
    {
        if ($room->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $room->delete();
        return response()->json();
    }
}
