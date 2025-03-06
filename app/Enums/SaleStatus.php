<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SaleStatus: string implements HasLabel
{
    case PROCESO = 'en-proceso';
    case CANCELADO = 'cancelado';
    case APLAZADO = 'aplazado';
    case COMPLETADO = 'completado';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROCESO => 'En Proceso',
            self::CANCELADO => 'Cancelado',
            self::APLAZADO => 'Aplazado',
            self::COMPLETADO => 'Completado',
        };
    }

    public static function getColor($state)
    {
        return match ($state) {
            'en-proceso' => 'warning',
            'cancelado' => 'danger',
            'aplazado' => 'info',
            'completado' => 'success'
        };
    }

    public static function getName(string $enum)
    {
        return self::from($enum)->getLabel();
    }
}
