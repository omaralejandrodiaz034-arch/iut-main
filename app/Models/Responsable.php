<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesMovimiento;

class Responsable extends Model
{
    use GeneratesMovimiento;

    protected $table = 'responsables';

    public $timestamps = false;

    protected $fillable = ['tipo_id', 'cedula', 'nombre', 'correo', 'telefono'];

    public function tipo()
    {
        return $this->belongsTo(TipoResponsable::class, 'tipo_id');
    }

    public function bienes()
    {
        // Antes los bienes estaban enlazados directamente por responsable_id en la tabla bienes.
        // Ahora la asignación de responsable se hace en la dependencia, por lo que
        // usamos hasManyThrough para obtener los bienes a través de dependencias.
        return $this->hasManyThrough(
            \App\Models\Bien::class,
            \App\Models\Dependencia::class,
            'responsable_id', // FK en dependencias hacia responsables
            'dependencia_id', // FK en bienes hacia dependencias
            'id', // PK en responsables
            'id'  // PK en dependencias
        );
    }

    /**
     * Relación: Un responsable puede estar asignado a varias dependencias.
     */
    public function dependencias()
    {
        return $this->hasMany(Dependencia::class, 'responsable_id');
    }
}
