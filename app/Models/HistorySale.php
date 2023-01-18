<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySale extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'total',
        'sale',
        'time',
        'price_time'
    ];
}
