<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $instances = Phone::where('user_id', '=', auth()->id());
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->id();
        $validator = Validator::make($request->all(), [
            'data' => ['required', 'array'],
            'data.*.id' => ['nullable', 'numeric'],
            'data.*.phone' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();

        $instances = [];
        foreach ($inputs['data'] as $el) {
            if (empty($el['id'])) {
                $el['user_id'] = $user;
                $instance = Phone::create($el);
            } else {
                $instance = Phone::where('id', $el['id'])
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
    public function show(Phone $phone)
    {
        if ($phone->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        return response()->json($phone);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phone $phone)
    {
        if ($phone->user_id != auth()->id()) {
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
            'logo' => ['nullable', 'file', 'mimes:jpg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $phone->update($inputs);
        return response()->json($phone);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phone $phone)
    {
        if ($phone->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $phone->delete();
        return response()->json();
    }
}
