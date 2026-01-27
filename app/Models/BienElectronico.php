<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienElectronico extends Model
{
    use HasFactory;

    protected $table = 'bienes_electronicos';

    protected $fillable = [
        'bien_id',
        'subtipo',
        'procesador',
        'memoria',
        'almacenamiento',
        'pantalla',
        'serial',
        'garantia',
    ];

    protected $casts = [
        'garantia' => 'date',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }
}
