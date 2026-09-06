<?php

namespace App\Jobs;

use App\Actions\Prescription\IssuePrescriptionAction;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssuePrescriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Prescription $prescription,
        public readonly User $doctor,
        public readonly ?string $signature = null,
        public readonly bool $saveSignature = false,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(IssuePrescriptionAction $action): Prescription
    {
        return $action->execute(
            $this->prescription,
            $this->doctor,
            $this->signature,
            $this->saveSignature
        );
    }
}
