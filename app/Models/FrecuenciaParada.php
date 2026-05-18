<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FrecuenciaParada extends Model
{
    protected $fillable = [
        'frecuencia_id',
        'ciudad_id',
        'orden',
        'minutos_desde_origen',
    ];

    public function frecuencia(): BelongsTo
    {
        return $this->belongsTo(Frecuencia::class);
    }

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class);
    }
}
