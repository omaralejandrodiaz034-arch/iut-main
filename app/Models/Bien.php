<?php

namespace App\Models;

use App\Enums\EstadoBien;
use App\Enums\TipoBien;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model Bien.
 *
 * @property int $id
 */
class Bien extends Model
{
    use HasFactory;

    // Tabla asociada
    protected $table = 'bienes';

    // Atributos asignables en masa
    protected $fillable = [
        'dependencia_id',
        'codigo',
        'descripcion',
        'precio',
        'fotografia',
        'ubicacion',
        'estado',
        'fecha_registro',
        'tipo_bien',
        'caracteristicas',

    ];

    // App\Models\Bien.php
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('codigo', 'LIKE', "%{$term}%")
                    ->orWhere('descripcion', 'LIKE', "%{$term}%")
                    ->orWhere('ubicacion', 'LIKE', "%{$term}%");
            });
        }
    }

    // Casts automáticos
    protected $casts = [
        'fecha_registro' => 'datetime',
        'estado' => EstadoBien::class, // Enum PHP 8.1+
        'tipo_bien' => TipoBien::class, // Enum PHP 8.1+
        'precio' => 'decimal:2',
        'caracteristicas' => 'array',
    ];

    // Relaciones
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    // Detalles por tipo
    public function electronico()
    {
        return $this->hasOne(BienElectronico::class, 'bien_id');
    }

    public function mobiliario()
    {
        return $this->hasOne(BienMobiliario::class, 'bien_id');
    }

    public function vehiculo()
    {
        return $this->hasOne(BienVehiculo::class, 'bien_id');
    }

    public function otro()
    {
        return $this->hasOne(BienOtro::class, 'bien_id');
    }
}
