<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\University;
use Illuminate\Http\JsonResponse;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        if (! $request->has('search')) {
            $universities = University::all();

            return $this->success(data: $universities);
        }
        $search = $request->input('search');
        $universities = University::whereLike(
            'name',
            "%$search%",
            false,
        )->get();

        return $this->success(
            __('messages.operation_success'),
            $universities,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university): JsonResponse
    {
        return $this->success(
            __('messages.operation_success'),
            $university,
        );
    }
}
