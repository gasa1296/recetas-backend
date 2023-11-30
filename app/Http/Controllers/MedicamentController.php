<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @todo add search
     */
    public function index()
    {
        return response()->json(Medicament::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo Add validations
     */
    public function store(Request $request)
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
    public function show(Medicament $medicament)
    {
        return response()->json($medicament);
    }

    /**
     * Update the specified resource in storage.
     * @todo Add validations
     */
    public function update(Request $request, Medicament $medicament)
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
    public function destroy(Medicament $medicament)
    {
        Storage::delete($medicament->image);
        $medicament->delete();
        return response()->json();
    }
}
