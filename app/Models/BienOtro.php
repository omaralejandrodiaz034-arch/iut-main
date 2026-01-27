<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienOtro extends Model
{
    use HasFactory;

    protected $table = 'bienes_otros';

    protected $fillable = [
        'bien_id',
        'especificaciones',
        'cantidad',
        'presentacion',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }
}
