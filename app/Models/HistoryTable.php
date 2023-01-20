<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryTable extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'day_id',
        'table_id',
        'time'
    ];

    public function Table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }
    
    public function Day()
    {
        return $this->belongsTo(Day::class, 'day_id');
    }
}
