<?php

use App\Enums\AppointmentStatus;
use App\Enums\PrescriptionStatus;

test('prescription status enum has expected cases and transitions', function () {
    expect(PrescriptionStatus::Draft->value)->toBe(0);
    expect(PrescriptionStatus::Active->value)->toBe(1);
    expect(PrescriptionStatus::PartiallyDispensed->value)->toBe(2);
    expect(PrescriptionStatus::FullyDispensed->value)->toBe(3);
    expect(PrescriptionStatus::Expired->value)->toBe(4);
    expect(PrescriptionStatus::Nulled->value)->toBe(5);

    expect(PrescriptionStatus::Draft->label())->toBe('Borrador');
    expect(PrescriptionStatus::Active->label())->toBe('Activa');

    expect(PrescriptionStatus::Draft->canBeModified())->toBeTrue();
    expect(PrescriptionStatus::Active->canBeModified())->toBeFalse();

    expect(PrescriptionStatus::Active->canBeDispensed())->toBeTrue();
    expect(PrescriptionStatus::PartiallyDispensed->canBeDispensed())->toBeTrue();
    expect(PrescriptionStatus::FullyDispensed->canBeDispensed())->toBeFalse();
    expect(PrescriptionStatus::Draft->canBeDispensed())->toBeFalse();

    expect(PrescriptionStatus::Active->canBeNullified())->toBeTrue();
    expect(PrescriptionStatus::Draft->canBeNullified())->toBeFalse();
});

test('appointment status enum has expected cases and labels', function () {
    expect(AppointmentStatus::Scheduled->value)->toBe('scheduled');
    expect(AppointmentStatus::Confirmed->value)->toBe('confirmed');
    expect(AppointmentStatus::Cancelled->value)->toBe('cancelled');

    expect(AppointmentStatus::Scheduled->label())->toBe('Programada');
    expect(AppointmentStatus::Cancelled->label())->toBe('Cancelada');

    expect(AppointmentStatus::values())->toContain('scheduled', 'confirmed', 'completed', 'cancelled');
});
