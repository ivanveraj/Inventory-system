<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'type',
        'name',
        'description',
        'category',
        'sku',
        'barcode',
        'keywords',
        'buyprice',
        'saleprice',
        'amount',
        'discount',
        'discount_to',
        'iva',
        'is_activated',
        'has_stock_alert',
        'min_stock_alert',
        'utility',
        'state'
    ];
}
