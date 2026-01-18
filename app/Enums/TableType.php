<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TableType: string implements HasLabel
{
    case WITH_TIME = 'con_tiempo';
    case WITHOUT_TIME = 'sin_tiempo';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::WITH_TIME => 'Con tiempo',
            self::WITHOUT_TIME => 'Sin tiempo',
        };
    }
}
