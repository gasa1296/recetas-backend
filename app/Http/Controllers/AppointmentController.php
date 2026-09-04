<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments for the authenticated doctor.
     */
    public function index(Request $request): JsonResponse
    {
        $query = auth()->user()->appointments()
            ->with(['patient', 'room', 'specialty']);

        if ($request->filled('from')) {
            $from = $this->parseDateTimeString($request->input('from'));
            $query->where('starts_at', '>=', $from);
        }

        if ($request->filled('to')) {
            $to = $this->parseDateTimeString($request->input('to'));
            $query->where('starts_at', '<=', $to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('identification', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->input('per_page', 15);
        $appointments = $query->orderBy('starts_at', 'asc')->paginate($perPage);

        return (new AppointmentCollection($appointments))->response();
    }

    /**
     * Store a newly created appointment.
     */
    public function store(AppointmentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $appointment = auth()->user()->appointments()->create($data);

        return $this->success(
            __('messages.operation_success'),
            new AppointmentResource($appointment->load(['patient', 'room', 'specialty', 'user'])),
            201
        );
    }

    /**
     * Display the specified appointment.
     */
    public function show(int $appointment): JsonResponse
    {
        $appointmentModel = auth()->user()->appointments()
            ->with(['patient', 'room', 'specialty', 'user'])
            ->findOrFail($appointment);

        return $this->success(
            __('messages.operation_success'),
            new AppointmentResource($appointmentModel)
        );
    }

    /**
     * Update the specified appointment.
     */
    public function update(AppointmentRequest $request, int $appointment): JsonResponse
    {
        $appointmentModel = auth()->user()->appointments()->findOrFail($appointment);
        $appointmentModel->update($request->validated());

        return $this->success(
            __('messages.operation_success'),
            new AppointmentResource($appointmentModel->load(['patient', 'room', 'specialty', 'user']))
        );
    }

    /**
     * Cancel and soft delete the appointment.
     */
    public function destroy(int $appointment): JsonResponse
    {
        $appointmentModel = auth()->user()->appointments()->findOrFail($appointment);
        $appointmentModel->update(['status' => Appointment::STATUS_CANCELLED]);
        $appointmentModel->delete();

        return $this->success(__('messages.operation_success'));
    }

    /**
     * Quick status transition for appointment workflow.
     */
    public function updateStatus(Request $request, int $appointment): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Appointment::VALID_STATUSES)],
        ]);

        $appointmentModel = auth()->user()->appointments()->findOrFail($appointment);
        $appointmentModel->update(['status' => $validated['status']]);

        return $this->success(
            __('messages.operation_success'),
            new AppointmentResource($appointmentModel->load(['patient', 'room', 'specialty', 'user']))
        );
    }

    /**
     * Safely parse and format date strings into Y-m-d H:i:s, correcting unencoded + signs from query strings.
     */
    protected function parseDateTimeString(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            $fixed = preg_replace('/(\d{2}:\d{2}:\d{2})\s(\d{2}:\d{2})$/', '$1+$2', $value);
            return Carbon::parse($fixed)->utc()->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return $value;
        }
    }
}
