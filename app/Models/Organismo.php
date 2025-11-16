<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesMovimiento;

/**
 * Eloquent model Organismo.
 *
 * @property int $id
 */
class Organismo extends Model
{
    use HasFactory, GeneratesMovimiento;

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
