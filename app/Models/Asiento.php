<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asiento extends Model
{
    protected $fillable = [
        'bus_id',
        'tipo_asiento_id',
        'numero',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function tipoAsiento(): BelongsTo
    {
        return $this->belongsTo(TipoAsiento::class);
    }

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class);
    }
}
