<?php

namespace App\Http\Controllers;

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
            $instances = Medicament::where('name', 'LIKE', "%$request->search%");
            return response()->json($instances->paginate(10));
        }
        return response()->json(Medicament::paginate(10));
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
     * @todo Add validations
     */
    public function update(Request $request, Medicament $medicament): JsonResponse
    {
        $inputs = $request->all();
        if ($request->file('image')) {
            $inputs['image'] = $request->file('image')->store('images');
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
