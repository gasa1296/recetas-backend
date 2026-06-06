<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescriptionRequest;
use App\Http\Resources\PrescriptionResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @todo add search
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $prescriptions = $user->prescriptions()->with(['patient', 'room'])->orderByDesc('created_at');

        return PrescriptionResource::collection($prescriptions->paginate(10))->response();
    }

    public function indexByPatient(int $patient): JsonResponse
    {
        $user = auth()->user();
        $prescriptions = $user->prescriptions()->with(['patient', 'room'])->where('patient_id', $patient)->orderByDesc('created_at');

        return PrescriptionResource::collection($prescriptions->paginate(10))->response();
    }

    public function indexByRoom(int $room): JsonResponse
    {
        $user = auth()->user();
        $prescriptions = $user->prescriptions()->with(['patient', 'room'])->where('room_id', $room)->orderByDesc('created_at');

        return PrescriptionResource::collection($prescriptions->paginate(10))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrescriptionRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $inputs['code'] = strtoupper(base_convert(Carbon::now()->getPreciseTimestamp(3), 10, 36));

        $user = auth()->user();
        $prescription = $user->prescriptions()->create($inputs);

        //TODO: store medicaments in prescription_medicaments table if medicaments dont exist in medicaments table, create them and store the id in prescription_medicaments table, if exist only store the id in prescription_medicaments table, if medicaments exist in prescription_medicaments table update the record with new data
        //TODO: handle generate PDF and send to patient
        return (new PrescriptionResource($prescription))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(int $prescription): JsonResponse
    {

        $user = auth()->user();
        $prescription = $user->prescriptions()->with(['patient', 'room'])->findOrFail($prescription);

        return (new PrescriptionResource($prescription))->response();
    }
}
