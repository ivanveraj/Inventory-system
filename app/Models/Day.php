<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'total',
        'profit'
    ];
    protected $dates = [
        'start_time',
    ];
}
