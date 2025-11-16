<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesMovimiento;

/**
 * Eloquent model Dependencia.
 *
 * @property int $id
 * @property int|null $responsable_id
 */
class Dependencia extends Model
{
    use HasFactory, GeneratesMovimiento;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'dependencias';

    /**
     * Atributos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
        'unidad_administradora_id',
        'codigo',
        'nombre',
        'responsable_id',
    ];

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
     * Relación: Una dependencia pertenece a una Unidad Administradora.
     */
    public function unidadAdministradora()
    {
        return $this->belongsTo(UnidadAdministradora::class);
    }

    /**
     * Relación: Una dependencia tiene muchos bienes.
     */
    public function bienes()
    {
        return $this->hasMany(Bien::class);
    }

    /**
     * Relación: La dependencia puede tener un responsable asignado.
     */
    public function responsable()
    {
        return $this->belongsTo(Responsable::class, 'responsable_id');
    }
}
