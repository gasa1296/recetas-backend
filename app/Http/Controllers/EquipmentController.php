<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @todo add search
     */
    public function index(Request $request): JsonResponse
    {
        if( $request->search ){
            $instances = Equipment::where('name', 'LIKE', "%$request->search%");
            return response()->json($instances->paginate(10));
        }
        return response()->json(Equipment::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo Add validations
     */
    public function store(Request $request): JsonResponse
    {
        $inputs = $request->all();
        if ($request->file('image')) {
            $inputs['image'] = $request->file('image')->store('images');
        }
        $instance = Equipment::create($inputs);
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment): JsonResponse
    {
        return response()->json($equipment);
    }

    /**
     * Update the specified resource in storage.
     * @todo Add validations
     */
    public function update(Request $request, Equipment $equipment): JsonResponse
    {
        $inputs = $request->all();
        if ($request->file('image')) {
            $inputs['image'] = $request->file('image')->store('images');
            if ($inputs['image']) {
                Storage::delete($equipment->image);
            }
        }
        $equipment->update($inputs);
        return response()->json($equipment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment): JsonResponse
    {
        Storage::delete($equipment->image);
        $equipment->delete();
        return response()->json();
    }
}
