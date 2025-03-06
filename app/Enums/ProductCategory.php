<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductCategory: string implements HasLabel
{
    case CERVEZA = 'cerveza';
    case LICOR = 'licor';
    case BEBIDA = 'bebida';
    case MECATO = 'mecato';
    case OTROS = 'otros';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CERVEZA => 'Cerveza',
            self::LICOR => 'Licor',
            self::BEBIDA => 'Bebida',
            self::MECATO => 'Mecato',
            self::OTROS => 'Otros',
        };
    }

    public static function getName(string $enum)
    {
        return self::from($enum)->getLabel();
    }
}
