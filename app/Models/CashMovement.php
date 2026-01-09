<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_id',
        'type',
        'concept',
        'amount',
        'payment_method',
        'user_id',
    ];

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
