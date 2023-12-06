<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultingRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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
     * @todo Add validations
     */
    public function store(Request $request): JsonResponse
    {
        $inputs = $request->all();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('medics/'.auth()->id());
        }
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
     * @todo Add validations
     */
    public function update(Request $request, ConsultingRoom $room): JsonResponse
    {
        if ($room->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $inputs = $request->all();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('medics/'.auth()->id());
            if ($inputs['logo']) {
                Storage::delete($room->image);
            }
        }
        $room->update($inputs);
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
        Storage::delete($room->logo);
        $room->delete();
        return response()->json();
    }
}
