<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CashMovementResource;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\Day;
use App\Models\CashMovement;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class DayDetail extends Page implements HasTable
{
    use InteractsWithTable, SettingTrait, TableTrait;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';
    protected string $view = 'filament.pages.day-detail';
    protected static ?string $navigationLabel = 'Detalle del Día';
    protected static ?string $title = 'Detalle del Día';

    public ?Day $day = null;
    public ?int $dayId = null;

    public function mount(?int $day = null): void
    {
        if ($day) {
            $this->dayId = $day;
            $this->day = Day::find($day);
        } else {
            // Si no se especifica, usar el día actual
            $this->day = getDayCurrent();
            $this->dayId = $this->day?->id;
        }
    }

    public function getHeading(): string | Htmlable
    {
        if (!$this->day) {
            return 'Día no encontrado';
        }
        
        return 'Detalle del Día - ' . $this->day->created_at->format('d/m/Y');
    }

    public function getDay(): ?Day
    {
        return $this->day;
    }

    protected function getHeaderWidgets(): array
    {
        if (!$this->day) {
            return [];
        }

        return [
            \App\Filament\Widgets\DayDetailStats::class,
        ];
    }

    public function table(Table $table): Table
    {
        if (!$this->dayId) {
            return $table->query(\App\Models\CashMovement::query()->whereRaw('1 = 0'));
        }

        return $table
            ->query(CashMovement::query()->where('day_id', $this->dayId))
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'income' => 'Ingreso',
                        'expense' => 'Gasto',
                        default => $state,
                    }),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->sortable()
                    ->color(fn(CashMovement $record) => $record->type === 'income' ? 'success' : 'danger'),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->heading('Movimientos de Caja del Día')
            ->emptyStateHeading('No hay movimientos registrados')
            ->emptyStateDescription('Agrega ingresos o gastos para este día.')
            ->emptyStateActions([
                \Filament\Tables\Actions\CreateAction::make()
                    ->label('Agregar Movimiento')
                    ->url(fn() => CashMovementResource::getUrl('create', ['day_id' => $this->dayId])),
            ]);
    }
}

