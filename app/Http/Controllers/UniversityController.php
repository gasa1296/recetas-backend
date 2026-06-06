<?php

namespace App\Http\Controllers;

use App\Models\University;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(University::all());
    }
}
