<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienMobiliario extends Model
{
    use HasFactory;

    protected $table = 'bienes_mobiliarios';

    protected $fillable = [
        'bien_id',
        'material',
        'dimensiones',
        'color',
        'capacidad',
        'cantidad_piezas',
        'acabado',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }
}
