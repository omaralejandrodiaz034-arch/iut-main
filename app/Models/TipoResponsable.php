<?php

namespace App\Models;

use App\Traits\GeneratesMovimiento;
use Illuminate\Database\Eloquent\Model;

class TipoResponsable extends Model
{
    use GeneratesMovimiento;

    protected $table = 'tipos_responsables'; // ✅ coincide con la migración

    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function responsables()
    {
        return $this->hasMany(Responsable::class, 'tipo_id');
    }
}
