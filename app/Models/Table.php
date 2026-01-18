<?php

namespace App\Models;

use App\Enums\TableType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'state',
        'type',
    ];

    protected $casts = [
        'type' => TableType::class,
    ];

    public function usesTime(): bool
    {
        return $this->type === TableType::WITH_TIME;
    }
}
