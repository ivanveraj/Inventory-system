<?php

namespace App\Filament\Pages;

use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\Day;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use App\Enums\PaymentMethods;
use App\Enums\ExpenseType;
use App\Traits\SaleTrait;
use App\Traits\NotificationTrait;
use Filament\Schemas\Components\Grid;
use App\Filament\Widgets\CashRegisterStats;
use App\Models\HistoryTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\Url;

class CashRegister extends Page implements HasTable, HasForms, HasActions
{
    use InteractsWithTable, InteractsWithForms, InteractsWithActions;
    use SettingTrait, TableTrait, SaleTrait, NotificationTrait;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected string $view = 'filament.pages.cash-register';
    protected ?string $subheading = 'Registra los ingresos y gastos de la caja';

    public $currentDay, $lastDay;
    public bool $existsDay = false;
    public bool $isHistoryDay = false;

    #[Url(as: 'day')]
    public ?int $dayId = null;

    public function mount()
    {
        if ($this->dayId) {
            $this->currentDay = Day::find($this->dayId);
            if (!$this->currentDay) {
                $this->customNotification('error', 'Error', 'No existe el día seleccionado.');
                return redirect()->to(self::getUrl());
            }
            $this->existsDay = true;
            $this->isHistoryDay = true;
            $this->lastDay = null;
            return;
        }

        $this->currentDay = getDayCurrent();
        $this->existsDay = $this->currentDay ? true : false;
        $this->lastDay = getLastDay2();
    }

    public function getTitle(): string
    {
        $dateLabel = $this->currentDay?->created_at?->format('d/m/Y') ?? 'Sin Día Activo';
        $prefix = $this->isHistoryDay ? 'Registro de Caja (Histórico): ' : 'Registro de Caja: ';
        return $prefix . $dateLabel;
    }

    public function getFormExpense($type)
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('amount')->label('Monto')
                        ->required()->numeric()->prefix('$'),
                    Select::make('payment_method')->label('Método de ' . $type)
                        ->options(PaymentMethods::class)
                        ->default('efectivo')->required(),
                    Textarea::make('concept')->label('Concepto')
                        ->required()->rows(4)->columnSpanFull()
                        ->maxLength(255),
                ])
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(HistoryTable::query()->where('day_id', $this->currentDay?->id ?? 0))
            ->heading('Recaudos en mesas')
            ->defaultSort('table.name', 'asc')
            ->paginated(false)
            ->columns([
                TextColumn::make('table.name')->label('Mesa')
                    ->sortable()->wrap()->searchable(),
                TextColumn::make('time')->label('Tiempo transcurrido')
                    ->sortable()->alignCenter()
                    ->formatStateUsing(fn($state) => $state . ' min (' . number_format($state / 60, 2) . ' h)'),
                TextColumn::make('total')->label('Total')
                    ->sortable()->alignCenter()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openCashRegister')->label('Abrir Caja')
                ->color('success')->icon('heroicon-o-lock-open')
                ->requiresConfirmation()->visible(fn() => !$this->existsDay && !$this->isHistoryDay)
                ->modalHeading('Abrir Caja')
                ->modalDescription(fn() => $this->lastDay ? 'Estas seguro de abrir la caja? Este proceso no es reversible.' : 'Asegúrate de ingresar el saldo inicial para comenzar el día.')
                ->modalSubmitActionLabel('Abrir Caja')
                ->schema(fn() => $this->lastDay ? [] : [
                    TextInput::make('opening_balance')->label('Saldo Inicial')
                        ->required()->numeric()->prefix('$')
                        ->default(0)->minValue(0),
                ])
                ->action(function ($data) {
                    $this->currentDay = getDay($this->lastDay ? $this->lastDay->cash_left_for_next_day : $data['opening_balance']);
                    $this->customNotification('success', 'Exito', 'Caja abierta correctamente');
                    return redirect()->to(Sales::getUrl());
                }),
            Action::make('registerExpense')->label('Gasto')
                ->hidden(fn() => !$this->existsDay || $this->isHistoryDay)
                ->color('danger')->icon('heroicon-s-plus')
                ->schema($this->getFormExpense('Pago'))
                ->requiresConfirmation()
                ->modalHeading('Nuevo Gasto')
                ->modalDescription('Asegurate registrar el gasto correctamente, esta accion no es reversible.')
                ->action(function ($data) {
                    $this->addCashMovement($this->currentDay->id, ExpenseType::GASTO->value, $data['concept'], $data['amount'], $data['payment_method'], Auth::id());
                    $this->dispatch('refreshTable');
                    $this->customNotification('success', 'Gasto registrado', "Gasto de \${$data['amount']} registrado correctamente");
                }),
            Action::make('registerIncome')->label('Ingreso')
                ->color('success')->icon('heroicon-s-plus')
                ->hidden(fn() => !$this->existsDay || $this->isHistoryDay)
                ->schema($this->getFormExpense('Pago'))
                ->requiresConfirmation()
                ->modalHeading('Nuevo Ingreso')
                ->modalDescription('Asegurate de registrar el ingreso correctamente, esta accion no es reversible.')
                ->action(function ($data) {
                    $this->addCashMovement($this->currentDay->id, ExpenseType::INGRESO->value, $data['concept'], $data['amount'], $data['payment_method'], Auth::id());
                    $this->dispatch('refreshTable');
                    $this->customNotification('success', 'Ingreso registrado', "Ingreso de \${$data['amount']} registrado correctamente");
                }),
            Action::make('registerWithdrawal')->label('Retiro')
                ->color('warning')->icon('heroicon-s-minus')
                ->hidden(fn() => !$this->existsDay || $this->isHistoryDay)
                ->schema($this->getFormExpense('Retiro'))
                ->requiresConfirmation()
                ->modalHeading('Nuevo Retiro')
                ->modalDescription('Asegurate de registrar el retiro correctamente, esta accion no es reversible.')
                ->action(function ($data) {
                    $this->addCashMovement($this->currentDay->id, ExpenseType::RETIRO->value, $data['concept'], $data['amount'], 'efectivo', Auth::id());
                    $this->dispatch('refreshTable');
                    $this->customNotification('success', 'Retiro registrado', "Retiro de \${$data['amount']} registrado correctamente");
                }),

        ];
    }

    protected function getHeaderWidgets(): array
    {
        if (!$this->existsDay) {
            return [];
        }
        return [
            CashRegisterStats::class,
        ];
    }

    public function getCurrentDay(): ?Day
    {
        return $this->currentDay;
    }
}
