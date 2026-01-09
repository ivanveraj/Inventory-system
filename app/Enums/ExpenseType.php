<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExpenseType: string implements HasLabel
{
    case COMISIONES = 'comisiones';
    case GASTO = 'gasto';
    case INGRESO = 'ingreso';
    case RETIRO = 'retiro';
    case APERTURA = 'apertura';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::COMISIONES => 'Comisiones',
            self::GASTO => 'Gasto',
            self::INGRESO => 'Ingreso',
            self::RETIRO => 'Retiro',
            self::APERTURA => 'Apertura'
        };
    }

    public static function getColor(string $state): ?string
    {
        return match ($state) {
            self::COMISIONES->value => 'primary',
            self::GASTO->value => 'danger',
            self::INGRESO->value => 'success',
            self::RETIRO->value => 'warning',
            self::APERTURA->value => 'info',
            default => 'gray',
        };
    }

    public static function getName(string $enum): ?string
    {
        return self::from($enum)->getLabel();
    }
}
