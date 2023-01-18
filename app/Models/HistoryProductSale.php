<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryProductSale extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale',
        'product_id',
        'amount',
        'price'
    ];
}
