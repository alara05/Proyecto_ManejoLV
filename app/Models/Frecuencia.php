<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Frecuencia extends Model
{
    protected $fillable = [
        'cooperativa_id',
        'ciudad_origen_id',
        'ciudad_destino_id',
        'hora_salida',
        'numero_resolucion_ant',
        'fecha_resolucion_ant',
        'tiene_paradas',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'fecha_resolucion_ant' => 'date',
            'tiene_paradas' => 'boolean',
            'activa' => 'boolean',
        ];
    }

    public function cooperativa(): BelongsTo
    {
        return $this->belongsTo(Cooperativa::class);
    }

    public function origen(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_origen_id');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_destino_id');
    }

    public function paradas(): HasMany
    {
        return $this->hasMany(FrecuenciaParada::class)->orderBy('orden');
    }

    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class);
    }
}
