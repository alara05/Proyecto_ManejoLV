<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoAsiento extends Model
{
    protected $fillable = [
        'cooperativa_id',
        'nombre',
        'descripcion',
        'recargo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'recargo' => 'decimal:2',
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
}
