<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\{
    Models\ConsultingRoom,
    Http\Requests\Room\StoreRequest,
    Http\Requests\Room\UpdateRequest,
};
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
    public function store(StoreRequest $request): JsonResponse
    {
        $user = auth()->id();
        $inputs = $request->validated();

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
                if (empty($request->file('logo')[$key]) && empty($el['logo'])) {
                    Storage::disk('public')->delete($instance->logo ?: '');
                    $el['file'] = '';
                }
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
    public function update(UpdateRequest $request, ConsultingRoom $room): JsonResponse
    {
        if ($room->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $inputs = $request->validated();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('medics/' . auth()->id(), 'public');
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
        if (!empty($room->logo)) {
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
