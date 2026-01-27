<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienVehiculo extends Model
{
    use HasFactory;

    protected $table = 'bienes_vehiculos';

    protected $fillable = [
        'bien_id',
        'marca',
        'modelo',
        'anio',
        'placa',
        'motor',
        'chasis',
        'combustible',
        'kilometraje',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }
}
