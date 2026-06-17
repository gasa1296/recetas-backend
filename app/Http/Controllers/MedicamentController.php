<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Medicament;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\MedicamentCollection;

class MedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        if (! $request->has('search')) {
            $medicaments = Medicament::paginate(10);

            return (new MedicamentCollection($medicaments))->response();
        }
        $search = $request->input('search');
        $medicaments = Medicament::whereLike(
            'active_ingredient',
            "%$search%",
            false,
        )->paginate(10);

        return (new MedicamentCollection($medicaments))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicament $medicament): JsonResponse
    {
        return $this->success(__('messages.operation_success'), $medicament);
    }
}
