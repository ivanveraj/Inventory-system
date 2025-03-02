<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id',
        'product_id',
        'price',
        'amount',
        'total'
    ];
    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function SaleTable()
    {
        return $this->belongsTo(SaleTable::class, 'sale_id');
    }
}
