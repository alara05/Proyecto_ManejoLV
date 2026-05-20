<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    protected $fillable = [
        'cooperativa_id',
        'numero',
        'placa',
        'marca_chasis',
        'marca_carroceria',
        'anio',
        'capacidad_total',
        'foto_path',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function cooperativa(): BelongsTo
    {
        return $this->belongsTo(Cooperativa::class);
    }

    public function asientos(): HasMany
    {
        return $this->hasMany(Asiento::class);
    }

    public function rutas(): HasMany
    {
        return $this->hasMany(Ruta::class);
    }
}
