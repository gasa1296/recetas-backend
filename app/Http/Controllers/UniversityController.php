<?php

namespace App\Http\Controllers;

use App\Models\University;

class UniversityController extends Controller
{
    public function __invoke()
    {
        return response()->json(University::all());
    }
}
