<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SupplierCategory: string implements HasLabel
{
    case BEBIDAS = 'bebidas';
    case ALIMENTOS = 'alimentos';
    case CUIDADO_PERSONAL = 'cuidado_personal';
    case OTROS = 'otros';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BEBIDAS => 'Bebidas',
            self::ALIMENTOS => 'Alimentos',
            self::CUIDADO_PERSONAL => 'Cuidado Personal',
            self::OTROS => 'Otros',
        };
    }

    public static function getName(string $enum)
    {
        return self::from($enum)->getLabel();
    }
}
