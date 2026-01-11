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
use App\Models\SaleTable;
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

class CashRegister extends Page implements HasTable, HasForms, HasActions
{
    use InteractsWithTable, InteractsWithForms, InteractsWithActions;
    use SettingTrait, TableTrait, SaleTrait, NotificationTrait;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected string $view = 'filament.pages.cash-register';
    protected ?string $subheading = 'Registra los ingresos y gastos de la caja';

    public ?Day $currentDay = null;
    public bool $existsDay = false;

    public function mount()
    {
        $this->currentDay = getDayCurrent();
        $this->existsDay = $this->currentDay ? true : false;
    }

    public function getTitle(): string
    {
        return 'Registro de Caja: ' . ($this->currentDay?->created_at->format('d/m/Y') ?? 'Sin Día Activo');
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
            ->columns([
                TextColumn::make('table.name')
                    ->label('Mesa')
                    ->sortable()->wrap()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->heading('Movimientos de Caja del Día')
            ->emptyStateHeading('No hay movimientos registrados')
            ->emptyStateDescription('Agrega ingresos o gastos para este día.')
        ;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('aa')->label('Abrir Caja')
                ->color('success')->icon('heroicon-o-lock-open')
                ->requiresConfirmation()
                ->modalHeading('Abrir Caja')
                ->modalDescription('Asegúrate de ingresar el saldo inicial para comenzar el día.')
                ->modalSubmitActionLabel('Abrir Caja')
                ->schema([
                    TextInput::make('opening_balance')
                        ->label('Saldo Inicial')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->step(0.01)
                        ->default(0),
                ])
                ->action(function ($data) {
                    // Verificar que no haya un día abierto
                    $existingDay = Day::where('status', 'open')->first();
                    if ($existingDay) {
                        $this->customNotification('error', 'Ya existe un día abierto', 'Ya existe un día abierto, cierra el día actual para abrir uno nuevo.');
                        return;
                    }

                    // // Crear nuevo día
                    $day = Day::create([
                        'opening_balance' => $data['opening_balance'],
                        'opened_at' => now(),
                        'opened_by' => Auth::id(),
                        'status' => 'open',
                        'total' => 0,
                        'profit' => 0,
                        'cash_sales' => 0,
                        'card_sales' => 0,
                         'transfer_sales' => 0,
                        'total_sales' => 0,
                        'tables_total' => 0,
                        'products_total' => 0,
                        'expenses' => 0,
                        'withdrawals' => 0,
                        'cash_left_for_next_day' => 0,
                        'final_balance' => $data['opening_balance'],
                    ]);

                    // // Crear registros de historial de mesas
                    $tables = $this->getTables();
                    foreach ($tables as $table) {
                        HistoryTable::create([
                            'day_id' => $day->id,
                            'table_id' => $table->id,
                            'time' => 0,
                            'total' => 0,
                        ]);
                    }

                    $this->currentDay = $day;
                    $this->dispatch('$refresh');

                    $this->customNotification('success', 'Caja abierta correctamente', 'Caja abierta correctamente');
                }),
            Action::make('registerExpense')->label('Gasto')
                ->hidden(fn() => !$this->existsDay)
                ->color('danger')->icon('heroicon-o-plus-circle')
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
                ->color('success')->icon('heroicon-o-plus-circle')
                ->hidden(fn() => !$this->existsDay)
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
                ->color('warning')->icon('heroicon-o-plus-circle')
                //->hidden(fn() => !$this->existsDay)
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
        if (!$this->currentDay) {
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
