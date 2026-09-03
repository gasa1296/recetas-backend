<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use App\Models\Appointment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
{
    use JsonValidationResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', Rule::exists('patients', 'id')->where('user_id', auth()->id())],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'specialty_id' => ['nullable', 'integer', 'exists:specialties,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', Rule::in(Appointment::VALID_STATUSES)],
            'reminder_channel' => ['sometimes', 'string', Rule::in(['email', 'whatsapp', 'sms'])],
            'reminder_enabled' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Configure the validator instance with anti-overlap checks.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $startsAt = $this->input('starts_at');
            $endsAt = $this->input('ends_at');

            if (! $startsAt || ! $endsAt) {
                return;
            }

            $startsAtTimestamp = strtotime($startsAt);
            $endsAtTimestamp = strtotime($endsAt);

            if (! $startsAtTimestamp || ! $endsAtTimestamp || $endsAtTimestamp <= $startsAtTimestamp) {
                return;
            }

            $currentAppointment = $this->route('appointment');
            $excludeId = is_object($currentAppointment) ? $currentAppointment->id : $currentAppointment;

            $userId = auth()->id();

            // 1. Doctor schedule conflict check
            $doctorConflict = Appointment::where('user_id', $userId)
                ->overlapping($startsAt, $endsAt, $excludeId)
                ->exists();

            if ($doctorConflict) {
                $validator->errors()->add('starts_at', 'El médico ya tiene una cita programada en ese rango de horario.');
            }

            // 2. Room schedule conflict check
            $roomId = $this->input('room_id');
            if ($roomId) {
                $roomConflict = Appointment::where('room_id', $roomId)
                    ->overlapping($startsAt, $endsAt, $excludeId)
                    ->exists();

                if ($roomConflict) {
                    $validator->errors()->add('room_id', 'El consultorio seleccionado ya está reservado en ese horario.');
                }
            }
        });
    }
}
