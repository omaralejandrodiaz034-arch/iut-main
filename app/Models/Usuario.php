<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\GeneratesMovimiento;

/**
 * Eloquent model Usuario.
 *
 * @property int $id
 * @property int $rol_id
 * @property bool $is_admin
 */
class Usuario extends Authenticatable
{
    use HasFactory, GeneratesMovimiento;

    /**
     * Tabla personalizada para el modelo de autenticación.
     */
    protected $table = 'usuarios';

    /**
     * Atributos que pueden ser asignados masivamente.
     */
    protected $fillable = [
        'rol_id',
        'cedula',
        'nombre',
        'apellido',
        'correo',
        'hash_password',
        'activo',
        'is_admin',
    ];

    protected $appends = ['nombre_completo'];

    public function getNombreCompletoAttribute()
    {
        return trim($this->nombre.' '.$this->apellido);
    }

    /**
     * Atributos ocultos en serializaciones.
     */
    protected $hidden = [
        'hash_password',
        'remember_token',
    ];

    /**
     * Casts automáticos para atributos.
     */
    protected $casts = [
        'activo' => 'boolean',
        'is_admin' => 'boolean',
    ];

    /**
     * Laravel usará este campo como contraseña.
     */
    public function getAuthPassword()
    {
        return $this->hash_password;
    }

    /**
     * Laravel usará este campo como identificador de login.
     */
    public function getAuthIdentifierName()
    {
        return 'id'; // Cambiar a ID
    }

    /**
     * Relación: Usuario pertenece a un Rol.
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    /**
     * Relación: Usuario tiene muchos Reportes.
     */
    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }

    /**
     * Relación: Usuario tiene muchos Movimientos.
     */
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    /**
     * Verifica si el usuario es administrador.
     */
    // En Usuario.php
    public function isAdmin(): bool
    {
        return $this->is_admin === true && $this->activo === true;
    }

    /**
     * Verifica si el usuario puede eliminar datos del sistema.
     * Solo administradores pueden eliminar datos (excepto otros administradores).
     */
    public function canDeleteData(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Verifica si el usuario puede eliminar a otro usuario.
     * Solo administradores pueden eliminar usuarios, pero no pueden eliminarse a sí mismos
     * ni a otros administradores.
     */
    public function canDeleteUser(Usuario $userToDelete): bool
    {
        // El usuario debe ser administrador
        if (! $this->canDeleteData()) {
            return false;
        }

        // No puede eliminarse a sí mismo
        if ($this->id === $userToDelete->id) {
            return false;
        }

        // No puede eliminar a otro administrador
        if ($userToDelete->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Verifica si el usuario puede crear nuevos administradores.
     */
    public function canCreateAdmin(): bool
    {
        return $this->isAdmin();
    }
}
