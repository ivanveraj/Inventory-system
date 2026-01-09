<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Tables\Columns\TextColumn;
use App\Models\CashMovement;
use App\Enums\ExpenseType;
use Livewire\Attributes\On;

class CashMovements extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithTable, InteractsWithActions, InteractsWithSchemas;

    public $currentDay;

    #[On('refreshTable')]
    public function refreshTable()
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table->heading('Movimientos de Caja')
            ->query(CashMovement::query()->where('day_id', $this->currentDay))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Responsable')
                    ->sortable()->searchable(),
                TextColumn::make('type')->label('Tipo')
                    ->badge()->color(fn($state) => ExpenseType::getColor($state))
                    ->formatStateUsing(fn($state) => ExpenseType::getName($state))
                    ->toggleable()->sortable(),
                TextColumn::make('concept')->label('Concepto')
                    ->limit(25)->toggleable()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) <= $column->getCharacterLimit() ? null : $state;
                    }),
                TextColumn::make('amount')->label('Valor')
                    ->formatStateUsing(fn($state) => formatMoney($state))
                    ->toggleable()->sortable(),
                TextColumn::make('created_at')->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable()->sortable(),
            ]);
    }

    public function render()
    {
        return view('livewire.cash-movements');
    }
}
