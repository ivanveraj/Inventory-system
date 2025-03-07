<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id',
        'name',
        'product_id',
        'history_p',
        'amount'
    ];
    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function HistoryProduct()
    {
        return $this->belongsTo(Product::class, 'history_p');
    }

    public function SaleTable()
    {
        return $this->belongsTo(SaleTable::class, 'sale_id');
    }
}
