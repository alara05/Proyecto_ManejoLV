<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Boleto extends Model
{
    protected $fillable = [
        'salida_id',
        'user_id',
        'vendido_por',
        'asiento_id',
        'ciudad_origen_id',
        'ciudad_destino_id',
        'codigo',
        'pasajero_nombre',
        'pasajero_cedula',
        'tipo_descuento',
        'porcentaje_descuento',
        'precio',
        'estado',
        'vendido_at',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje_descuento' => 'decimal:2',
            'precio' => 'decimal:2',
            'vendido_at' => 'datetime',
        ];
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(Salida::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendido_por');
    }

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(Asiento::class);
    }

    public function origen(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_origen_id');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_destino_id');
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }

    public function registrosAcceso(): HasMany
    {
        return $this->hasMany(RegistroAcceso::class);
    }
}
