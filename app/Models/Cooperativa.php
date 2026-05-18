<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cooperativa extends Model
{
    protected $fillable = [
        'nombre',
        'ruc',
        'telefono',
        'email',
        'direccion',
        'logo_path',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }

    public function frecuencias(): HasMany
    {
        return $this->hasMany(Frecuencia::class);
    }

    public function rutas(): HasMany
    {
        return $this->hasMany(Ruta::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
