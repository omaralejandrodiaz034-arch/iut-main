<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eliminado extends Model
{
    use HasFactory;

    protected $table = 'eliminados';

    protected $fillable = [
        'model_type',
        'model_id',
        'data',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'deleted_at' => 'datetime',
    ];
}
