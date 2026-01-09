<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CashMovementResource;
use App\Filament\Pages\Sales;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\CashMovement;
use App\Models\Day;
use App\Models\HistorySale;
use App\Models\HistoryTable as HistoryTableModel;
use App\Models\Table as TableModel;
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

class CashRegister extends Page implements HasTable, HasActions
{
    use InteractsWithTable, SettingTrait, TableTrait, SaleTrait, NotificationTrait;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected string $view = 'filament.pages.cash-register';
    protected static ?string $navigationLabel = 'Caja';
    protected static ?string $title = 'Caja';

    public ?Day $currentDay = null;

    public function mount()
    {
        $this->currentDay = getDayCurrent();
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
            ->query(SaleTable::query())
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
            Action::make('registerExpense')->label('Gasto')
                ->color('danger')
                ->icon('heroicon-o-plus-circle')
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
                ->color('success')
                ->icon('heroicon-o-plus-circle')
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
                ->color('warning')
                ->icon('heroicon-o-plus-circle')
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
            \App\Filament\Widgets\CashRegisterStats::class,
        ];
    }

    public function openCashRegisterAction(): Action
    {
        return Action::make('openCashRegister')->label('Abrir Caja')
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
            ->action(function (array $data) {
                // Verificar que no haya un día abierto
                $existingDay = Day::where('status', 'open')->first();
                if ($existingDay) {
                    \Filament\Notifications\Notification::make()
                        ->title('Ya existe un día abierto')
                        ->danger()
                        ->send();
                    return;
                }

                // Crear nuevo día
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

                // Crear registros de historial de mesas
                $tables = TableModel::where('state', 1)->orderBy('id', 'ASC')->get();
                foreach ($tables as $table) {
                    HistoryTableModel::create([
                        'day_id' => $day->id,
                        'table_id' => $table->id,
                        'time' => 0,
                        'total' => 0
                    ]);
                }

                $this->currentDay = $day;
                $this->dispatch('$refresh');

                \Filament\Notifications\Notification::make()
                    ->title('Caja abierta correctamente')
                    ->success()
                    ->send();
            })
            ->visible(fn() => $this->currentDay === null);
    }

    public function getCurrentDay(): ?Day
    {
        return $this->currentDay;
    }
}
