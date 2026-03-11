<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use App\Traits\GeneratesMovimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasFactory, GeneratesMovimiento, AuditableTrait;

    protected $table = 'usuarios';

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

    protected $casts = [
        'activo' => 'boolean',
        'is_admin' => 'boolean',
    ];

    protected $hidden = [
        'hash_password',
        'remember_token',
    ];

    /**
     * Lógica de normalización centralizada
     */
    public static function normalizeCedula(?string $raw): string
{
    if (empty($raw)) return '';
    $digits = preg_replace('/\D/', '', $raw); // Solo números
    if (empty($digits)) return '';

    // Esto generará V-12.345.678 o V-7.123.456 correctamente
    return 'V-' . number_format((int)$digits, 0, '', '.');
}

    public function getNombreCompletoAttribute()
    {
        return trim($this->nombre.' '.$this->apellido);
    }

    public function getAuthPassword() { return $this->hash_password; }
    public function getAuthIdentifierName() { return 'id'; }

    public function rol() { return $this->belongsTo(Rol::class); }
    public function reportes() { return $this->hasMany(Reporte::class); }
    public function movimientos() { return $this->hasMany(Movimiento::class); }

    public function isAdmin(): bool
    {
        return $this->is_admin === true && $this->activo === true;
    }

    public function canDeleteData(): bool { return $this->isAdmin(); }

    public function canDeleteUser(Usuario $userToDelete): bool
    {
        if (!$this->canDeleteData()) return false;
        if ($this->id === $userToDelete->id) return false;
        if ($userToDelete->isAdmin()) return false;
        return true;
    }
}