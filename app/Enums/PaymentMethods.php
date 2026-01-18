<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethods: string implements HasLabel
{
    case EFECTIVO = 'efectivo';
    case TRANSFERENCIA = 'transferencia';
    case TARJETA = 'tarjeta';
    case OTRO = 'Otro metodo';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EFECTIVO => 'Efectivo',
            self::TRANSFERENCIA => 'Transferencia',
            self::TARJETA => 'Tarjeta',
            self::OTRO => 'Otro metodo'
        };
    }

    public static function getName(string $enum): ?string
    {
        return self::from($enum)->getLabel();
    }
}
