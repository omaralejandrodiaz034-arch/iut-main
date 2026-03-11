<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    public $timestamps = false;

    protected $table = 'auditoria';

    protected $fillable = [
        'usuario_id',
        'tabla',
        'registro_id',
        'operacion',
        'valores_anteriores',
        'valores_nuevos',
        'descripcion',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'valores_anteriores' => 'json',
        'valores_nuevos' => 'json',
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Registra un evento en la auditoría
     */
    public static function registrar(
        string $tabla,
        int $registro_id,
        string $operacion,
        ?array $valores_anteriores = null,
        ?array $valores_nuevos = null,
        ?string $descripcion = null,
        ?int $usuario_id = null,
        ?string $ip_address = null,
        ?string $user_agent = null
    ): self {
        return self::create([
            'usuario_id' => $usuario_id ?? auth()->id(),
            'tabla' => $tabla,
            'registro_id' => $registro_id,
            'operacion' => $operacion,
            'valores_anteriores' => $valores_anteriores,
            'valores_nuevos' => $valores_nuevos,
            'descripcion' => $descripcion,
            'ip_address' => $ip_address ?? request()->ip(),
            'user_agent' => $user_agent ?? request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
