<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ciudad extends Model
{
    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'provincia',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }

    public function rutasOrigen(): HasMany
    {
        return $this->hasMany(Ruta::class, 'ciudad_origen_id');
    }

    public function rutasDestino(): HasMany
    {
        return $this->hasMany(Ruta::class, 'ciudad_destino_id');
    }
}
