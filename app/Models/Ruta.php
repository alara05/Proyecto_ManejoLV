<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ruta extends Model
{
    protected $fillable = [
        'cooperativa_id',
        'bus_id',
        'ciudad_origen_id',
        'ciudad_destino_id',
        'nombre',
        'tipo_viaje',
        'distancia_km',
        'duracion_minutos',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'distancia_km' => 'decimal:2',
            'activa' => 'boolean',
        ];
    }

    public function cooperativa(): BelongsTo
    {
        return $this->belongsTo(Cooperativa::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function origen(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_origen_id');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_destino_id');
    }
}
