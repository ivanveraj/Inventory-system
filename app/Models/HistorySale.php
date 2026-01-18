<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySale extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'day_id',
        'client',
        'total',
        'time',
        'price_time',
        'user_id'
    ];

    public function day()
    {
        return $this->belongsTo(Day::class, 'day_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(HistoryProductSale::class, 'history_sale');
    }
}
