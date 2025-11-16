<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'movimientos';

    public $timestamps = false;

    // Allow storing a polymorphic subject (organismo, unidad, dependencia, bien, usuario, ...)
    protected $fillable = ['bien_id', 'subject_type', 'subject_id', 'tipo', 'fecha', 'observaciones', 'usuario_id'];

    protected $casts = ['fecha' => 'datetime'];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

        public function historialMovimientos()
    {
        return $this->hasMany(HistorialMovimiento::class, 'movimiento_id');
    }

    public function subject()
    {
        return $this->morphTo(null, 'subject_type', 'subject_id');
    }
}
