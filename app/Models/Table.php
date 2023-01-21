<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'state'
    ];
    public function Table()
    {
        return $this->hasMany(HistoryTable::class, 'id'); 
    }
}
