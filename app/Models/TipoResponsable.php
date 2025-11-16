<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesMovimiento;

class TipoResponsable extends Model
{
    use GeneratesMovimiento;

    protected $table = 'tipos_responsable';

    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function responsables()
    {
        return $this->hasMany(Responsable::class, 'tipo_id');
    }
}
