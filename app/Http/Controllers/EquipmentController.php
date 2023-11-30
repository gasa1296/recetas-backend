<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @todo add search
     */
    public function index()
    {
        return response()->json(Equipment::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function store(Request $request)
    {
        $instance = Equipment::create($request->all());
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        return response()->json($equipment);
    }

    /**
     * Update the specified resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function update(Request $request, Equipment $equipment)
    {
        $equipment->update($request->all());
        return response()->json($equipment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return response()->json();
    }
}
