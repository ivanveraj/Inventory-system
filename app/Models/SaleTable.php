<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleTable extends Model
{
    use HasFactory;
    protected $fillable = [
        'table_id',
        'state',
        'type',
        'client'
    ];
    protected $dates = [
        'start_time',
    ];
    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }
    public function extras()
    {
        return $this->hasMany(Extra::class, 'sale_id');
    }
}
