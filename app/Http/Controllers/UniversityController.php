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
            $universities = University::paginate(200);
        } else {
            $search = $request->input('search');
            $universities = University::whereLike(
                'name',
                "%$search%",
                false,
            )->paginate(200);
        }

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
