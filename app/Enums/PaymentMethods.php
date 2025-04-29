<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethods: string implements HasLabel
{
    case EFECTIVO = 'efectivo';
    case NEQUI = 'nequi';
    case DAVIPLATA = 'daviplata';
    case AHORRO_A_LA_MANO = 'ahorro-a-la-mano';
    case BANCOLOMBIA = 'bancolombia';
    case TRANSFIYA = 'transfiya';
    case OTRO='OTRO';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EFECTIVO => 'Efectivo',
            self::NEQUI => 'Nequi',
            self::DAVIPLATA => 'Daviplata',
            self::AHORRO_A_LA_MANO => 'Ahorro a la mano',
            self::BANCOLOMBIA => 'Bancolombia',
            self::TRANSFIYA => 'Transfiya',
            self::OTRO=>'Otro metodo'
        };
    }

    public static function getName(string $enum): ?string
    {
        return self::from($enum)->getLabel();
    }
}