<?php

namespace App\Enums;

enum PrescriptionStatus: int
{
    case Draft = 0;
    case Active = 1;
    case PartiallyDispensed = 2;
    case FullyDispensed = 3;
    case Expired = 4;
    case Nulled = 5;

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Active => 'Activa',
            self::PartiallyDispensed => 'Dispensada Parcial',
            self::FullyDispensed => 'Dispensada Total',
            self::Expired => 'Vencida',
            self::Nulled => 'Anulada',
        };
    }

    public function canBeDispensed(): bool
    {
        return in_array($this, [self::Active, self::PartiallyDispensed], true);
    }

    public function canBeModified(): bool
    {
        return $this === self::Draft;
    }

    public function canBeNullified(): bool
    {
        return $this === self::Active;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
