<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'profit',
        'finish_day',
        'opened_at',
        'closed_at',
        'opening_balance',
        'cash_sales',
        'card_sales',
        'transfer_sales',
        'total_sales',
        'tables_total',
        'products_total',
        'expenses',
        'withdrawals',
        'cash_left_for_next_day',
        'final_balance',
        'opened_by',
        'closed_by',
        'status',
    ];

    protected $casts = [
        'finish_day' => 'datetime',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'total' => 'decimal:2',
        'profit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'cash_sales' => 'decimal:2',
        'card_sales' => 'decimal:2',
        'transfer_sales' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'tables_total' => 'decimal:2',
        'products_total' => 'decimal:2',
        'expenses' => 'decimal:2',
        'withdrawals' => 'decimal:2',
        'cash_left_for_next_day' => 'decimal:2',
        'final_balance' => 'decimal:2',
    ];

    public function cashMovements()
    {
        return $this->hasMany(CashMovement::class);
    }

    public function incomes()
    {
        return $this->hasMany(CashMovement::class)->where('type', 'income');
    }

    public function expenses()
    {
        return $this->hasMany(CashMovement::class)->where('type', 'expense');
    }

    public function getTotalIncomesAttribute()
    {
        return $this->incomes()->sum('amount');
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getNetProfitAttribute()
    {
        return $this->profit + $this->total_incomes - $this->total_expenses;
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}