<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para bienes desincorporados.
 * Al desincorporsar un bien, se copian todos sus datos a esta tabla
 * y se elimina de la tabla principal de bienes.
 *
 * @property int $id
 * @property int $bien_id
 * @property int|null $dependencia_id
 * @property int|null $responsable_id
 * @property string $codigo
 * @property string $descripcion
 * @property float $precio
 * @property string|null $fotografia
 * @property string|null $ubicacion
 * @property string $estado
 * @property string|null $tipo_bien
 * @property array|null $caracteristicas
 * @property string $motivo_desincorporacion
 * @property string|null $acta_desincorporacion
 * @property \Carbon\Carbon $fecha_desincorporacion
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class BienDesincorporado extends Model
{
    use HasFactory;

    protected $table = 'bienes_desincorporados';

    protected $fillable = [
        'bien_id',
        'dependencia_id',
        'responsable_id',
        'codigo',
        'descripcion',
        'precio',
        'fotografia',
        'estado',
        'fecha_registro',
        'tipo_bien',
        'caracteristicas',
        'motivo_desincorporacion',
        'acta_desincorporacion',
        'fecha_desincorporacion',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'caracteristicas' => 'array',
        'fecha_registro' => 'date',
        'fecha_desincorporacion' => 'datetime',
    ];

    /**
     * Relación con el bien original (ya no existe, pero guardamos la referencia)
     */
    public function bien()
    {
        return $this->belongsTo(Bien::class, 'bien_id');
    }

    /**
     * Relación con la dependencia
     */
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class);
    }

    /**
     * Relación con el responsable
     */
    public function responsable()
    {
        return $this->belongsTo(Responsable::class);
    }
}
