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
    use HasFactory, GeneratesMovimiento, AuditableTrait;

    protected $table = 'organismos';

    protected $fillable = [
        'codigo',
        'nombre',
    ];

    /**
     * Un organismo tiene muchas Unidades Administradoras.
     */
    public function unidadesAdministradoras()
    {
        return $this->hasMany(UnidadAdministradora::class);
    }
}
