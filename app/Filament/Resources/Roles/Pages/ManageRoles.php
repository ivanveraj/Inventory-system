<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageRoles extends ManageRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Añadir rol')
                ->icon('heroicon-o-plus')
                ->modalHeading('Crear Rol')
                ->mutateDataUsing(function (array $data): array {
                    $data['guard_name'] = 'web';
                    return $data;
                }),
        ];
    }
}
