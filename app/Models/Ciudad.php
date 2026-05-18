<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ciudad extends Model
{
    protected $table = 'ciudades';

    protected $fillable = [
        'provincia_id',
        'nombre',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    public function rutasOrigen(): HasMany
    {
        return $this->hasMany(Ruta::class, 'ciudad_origen_id');
    }

    public function rutasDestino(): HasMany
    {
        return $this->hasMany(Ruta::class, 'ciudad_destino_id');
    }

    public function frecuenciasOrigen(): HasMany
    {
        return $this->hasMany(Frecuencia::class, 'ciudad_origen_id');
    }

    public function frecuenciasDestino(): HasMany
    {
        return $this->hasMany(Frecuencia::class, 'ciudad_destino_id');
    }

    public function paradas(): HasMany
    {
        return $this->hasMany(FrecuenciaParada::class);
    }

    public function boletosOrigen(): HasMany
    {
        return $this->hasMany(Boleto::class, 'ciudad_origen_id');
    }

    public function boletosDestino(): HasMany
    {
        return $this->hasMany(Boleto::class, 'ciudad_destino_id');
    }
}
