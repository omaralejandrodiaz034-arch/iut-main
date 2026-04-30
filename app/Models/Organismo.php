<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use App\Traits\GeneratesMovimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model Organismo.
 *
 * @property int $id
 */
class Organismo extends Model
{
    use AuditableTrait, GeneratesMovimiento, HasFactory;

    protected $table = 'organismos';

    protected $fillable = [
        'codigo',
        'nombre',
        'code_min',
        'code_max',
    ];

    /**
     * Un organismo tiene muchas Unidades Administradoras.
     */
    public function unidadesAdministradoras()
    {
        return $this->hasMany(UnidadAdministradora::class);
    }
}
