<?php

namespace App\Http\Controllers;

use Validator;
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
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->id();
        $validator = Validator::make($request->all(), [
            'data' => ['required', 'array'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();

        $instances = [];
        foreach ($inputs['data'] as $key => $el) {
            $el['user_id'] = $user;

            if (!empty($request->file('logo')[$key])) {
                $el['logo'] = $request->file('logo')[$key]->store('medics/' . $user);
            }
            if (empty($el['id'])) {
                $instance = ConsultingRoom::create($el);
            } else {
                $instance = ConsultingRoom::findOrFail($el['id']);
                $instance->update($el);
            }
            array_push($instances, $instance);
        }
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
     */
    public function update(Request $request, ConsultingRoom $room): JsonResponse
    {
        if ($room->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'zip' => 'required',
            'street' => 'required',
            'colony' => 'required',
            'state' => 'required',
            'delegation' => 'required',
            'n_exterior' => 'required',
            'design' => 'required',
            'logo' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
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
