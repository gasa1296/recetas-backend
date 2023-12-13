<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $instances = Specialization::where("user_id", auth()->id());
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
            'data.*.name' => ['required', 'string'],
            'data.*.identification' => 'required',
            'data.*.university' => ['nullable', 'string'],
            'logo' => ['required', 'array'],
            'logo.*' => ['required', 'file'],

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
            if(empty($el['id'])) {
                $el['user_id'] = $user;
                $instance = Specialization::create($el);
            } else {
                $instance = Specialization::where('id', $el['id'])
                    ->where('user_id', auth()->id())
                    ->fistOrFail();
                $instance->update($el);
            }
            array_push($instances, $instance);
        }
        return response()->json($instances);
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization $specialization): JsonResponse
    {
        if ($specialization->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        return response()->json($specialization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specialization $specialization): JsonResponse
    {
        if ($specialization->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'identification' => 'required',
            'university' => ['nullable', 'string'],
            'logo' => ['nullable', 'file'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('medics/' . auth()->id(), 'public');
            if ($inputs['logo'] && !empty($specialization->logo)) {
                Storage::delete($specialization->logo);
            }
        }
        $specialization->update($inputs);
        return response()->json($specialization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization $specialization): JsonResponse
    {
        if ($specialization->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        if (!empty($specialization->logo)) {
            Storage::delete($specialization->logo);
        }
        $specialization->delete();
        return response()->json();
    }
}
