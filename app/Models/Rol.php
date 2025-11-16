<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesMovimiento;

class Rol extends Model
{
    use GeneratesMovimiento;

    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = ['nombre', 'permisos'];

    protected $casts = [
        'permisos' => 'array',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
