<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'amount',
        'buyprice',
        'saleprice',
        'utility'
    ];
    
    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
}
