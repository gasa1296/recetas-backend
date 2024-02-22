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
            'data.*.id' => ['nullable', 'numeric'],
            'data.*.name' => ['required', 'string'],
            'data.*.zip' => ['required', 'string'],
            'data.*.street' => ['required', 'string'],
            'data.*.colony' => ['required', 'string'],
            'data.*.state' => ['required', 'string'],
            'data.*.delegation' => ['required', 'string'],
            'data.*.n_exterior' => ['required',],
            'data.*.n_interior' => ['nullable',],
            'data.*.address' => ['nullable', 'string'],
            'data.*.phone' => ['nullable', 'string'],
            'data.*.design' => ['nullable', 'string'],
            'logo' => ['nullable', 'array'],
            'logo.*' => ['nullable', 'file'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();

        $instances = [];
        foreach ($inputs['data'] as $key => $el) {
            if (!empty($request->file('logo')[$key])) {
                $el['logo'] = $request->file('logo')[$key]->store('medics/' . $user, 'public');
            }
            if (empty($el['id'])) {
                $el['user_id'] = $user;
                $instance = ConsultingRoom::create($el);
            } else {
                $instance = ConsultingRoom::where('id', $el['id'])
                    ->where('user_id', auth()->id())
                    ->firstOrFail();
                $instance->update($el);
            }
            array_push($instances, $instance);
        }
        return response()->json($instances);
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
            'name' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'street' => ['required', 'string'],
            'colony' => ['required', 'string'],
            'state' => ['required', 'string'],
            'delegation' => ['required', 'string'],
            'n_exterior' => ['required',],
            'n_interior' => ['nullable',],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'design' => ['nullable', 'string'],
            'logo' => ['nullable', 'file'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('medics/'.auth()->id(), 'public');
            if ($inputs['logo'] && !empty($room->logo)) {
                Storage::delete($room->logo);
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
        if (!empty($room->logo)){
            Storage::delete($room->logo);
        }
        $room->delete();
        return response()->json();
    }
    public function getFormats(): JsonResponse
    {
        return response()->json([0 => env('F1'), 1 => env('F2'), '2' => env('F3')]);
    }
}
