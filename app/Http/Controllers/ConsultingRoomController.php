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
    public function index(Request $request): JsonResponse
    {
        $instances = ConsultingRoom::where("user_id", $request->user()->id);
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
        $inputs["user_id"] = $request->user()->id;
        $instance = ConsultingRoom::create($inputs);
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $instance = ConsultingRoom::where([
            "user_id" => $request->user()->id,
            "id" => $id,
        ])->firstOrFail();
        return response()->json($instance);
    }

    /**
     * Update the specified resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $instance = ConsultingRoom::where([
            "user_id" => $request->user()->id,
            "id" => $id,
        ])->firstOrFail();
        $instance->update($request->all());
        return response()->json($instance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $instance = ConsultingRoom::where([
            "user_id", $request->user()->id,
            "id", $id,
        ])->firstOrFail();
        $instance->delete();
        return response()->json();
    }
}
