<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PatientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        $patients = auth()->user()->patients();
        if (! $request->has('search')) {
            $patients = $patients->paginate(10);

            return $this->success(__('messages.operation_success'), PatientResource::collection($patients));
        }

        $search = $request->input('search');
        $patients = $patients
            ->where(function ($query) use ($search) {
                $query->where(DB::raw("CONCAT_WS(' ', first_name, last_name1, last_name2)"), 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->paginate(10);

        return $this->success(__('messages.operation_success'), PatientResource::collection($patients));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientRequest $request): JsonResponse
    {
        $patients = auth()->user()->patients()->create($request->validated());

        return $this->success(__('messages.operation_success'), new PatientResource($patients));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $patient): JsonResponse
    {
        $patients = auth()->user()->patients()->findOrFail($patient);

        return $this->success(__('messages.operation_success'), new PatientResource($patients));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientRequest $request, int $patient): JsonResponse
    {
        $patients = auth()->user()->patients()->findOrFail($patient);
        $patients->update($request->validated());

        return $this->success(__('messages.operation_success'), new PatientResource($patients));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $patient): JsonResponse
    {
        $patients = auth()->user()->patients()->findOrFail($patient);
        $patients->delete();

        return $this->success(__('messages.operation_success'));
    }
}
