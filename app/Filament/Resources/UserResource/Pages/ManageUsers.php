<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Arr;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo Usuario')
                ->icon('heroicon-o-plus')
                ->slideOver()
                ->modalHeading('Crear Nuevo Usuario')
                ->mutateDataUsing(function (array $data) {
                    $data['status'] = true;
                    $data['password'] = Hash::make('Qazxcv08@');
                    return $data;
                })
                ->after(function ($record, array $data): void {
                    if (isset($data['role'])) {
                        $record->assignRole($data['role']);
                    }
                }),
        ];
    }
}
