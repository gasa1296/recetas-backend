<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;

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
     * @todo upload file
     * @todo Add validations
     */
    public function store(Request $request)
    {
        $instance = Medicament::create($request->all());
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
     * @todo upload file
     * @todo Add validations
     */
    public function update(Request $request, Medicament $medicament)
    {
        $medicament->update($request->all());
        return response()->json($medicament);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicament $medicament)
    {
        $medicament->delete();
        return response()->json();
    }
}
