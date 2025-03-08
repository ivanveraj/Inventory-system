<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryProductSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_sale',
        'product_id',
        'amount',
        'price'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function sale()
    {
        return $this->belongsTo(HistorySale::class, 'history_sale');
    }
}
