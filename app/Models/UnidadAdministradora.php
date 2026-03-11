<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use App\Traits\GeneratesMovimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model UnidadAdministradora.
 *
 * @property int $id
 */
class UnidadAdministradora extends Model
{
    use HasFactory, GeneratesMovimiento, AuditableTrait;

    /**
     * Nombre de la tabla asociada.
     * Especificado porque el plural es irregular.
     */
    protected $table = 'unidades_administradoras';

    /**
     * Atributos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
        'organismo_id',
        'codigo',
        'nombre',
    ];

    // App\Models\UnidadAdministradora.php
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('codigo', 'LIKE', "%{$term}%")
                    ->orWhere('nombre', 'LIKE', "%{$term}%");
            });
        }
    }

    /**
     * Relación: Una Unidad Administradora pertenece a un Organismo.
     */
    public function organismo()
    {
        return $this->belongsTo(Organismo::class);
    }

    /**
     * Relación: Una Unidad Administradora tiene muchas Dependencias.
     */
    public function dependencias()
    {
        return $this->hasMany(Dependencia::class);
    }
}
