<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Medicament;
use Illuminate\Http\JsonResponse;

class MedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        if (! $request->has('search')) {
            $medicaments = Medicament::paginate(10);

            return $this->success(data: $medicaments);
        }
        $search = $request->input('search');
        $medicaments = Medicament::whereLike(
            'name',
            "%$search%",
            false,
        )->paginate(10);

        return $this->success(data: $medicaments);
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicament $medicament): JsonResponse
    {
        return $this->success(data: $medicament);
    }
}
