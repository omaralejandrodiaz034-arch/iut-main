<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienDesincorporado extends Model
{
    use HasFactory;

    protected $table = 'bienes_desincorporados';

    protected $fillable = [
        'bien_id',
        'motivo_desincorporacion',
        'acta_desincorporacion',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }
}
