<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->search) {
            $instances = Medicament::where('name', 'LIKE', "%$request->search%")
                ->orWhere('ingredient', 'LIKE', "%$request->search%");
            return response()->json($instances->paginate(10));
        }
        return response()->json(Medicament::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'ingredient' => ['required', 'string'],
            'dose' => ['required', 'string'],
            'quantity' => ['required', 'string'],
            'image' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        if ($request->file('image')) {
            $inputs['image'] = $request->file('image')->store('images', 'public');
        }
        $instance = Medicament::create($inputs);
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicament $medicament): JsonResponse
    {
        return response()->json($medicament);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicament $medicament): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'ingredient' => ['required', 'string'],
            'dose' => ['required', 'string'],
            'quantity' => ['required', 'string'],
            'image' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        if ($request->file('image')) {
            $inputs['image'] = $request->file('image')->store('images', 'public');
            if ($inputs['image']) {
                Storage::delete($medicament->image);
            }
        }
        $medicament->update($inputs);
        return response()->json($medicament);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicament $medicament): JsonResponse
    {
        Storage::delete($medicament->image);
        $medicament->delete();
        return response()->json();
    }
}
