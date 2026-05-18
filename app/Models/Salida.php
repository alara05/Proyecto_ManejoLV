<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salida extends Model
{
    protected $fillable = [
        'frecuencia_id',
        'bus_id',
        'fecha',
        'hora_salida',
        'estado',
        'precio_base',
        'generada_automaticamente',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'precio_base' => 'decimal:2',
            'generada_automaticamente' => 'boolean',
        ];
    }

    public function frecuencia(): BelongsTo
    {
        return $this->belongsTo(Frecuencia::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class);
    }
}
