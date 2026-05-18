<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'boleto_id',
        'validado_por',
        'metodo',
        'monto',
        'comprobante_path',
        'estado',
        'validado_at',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'validado_at' => 'datetime',
        ];
    }

    public function boleto(): BelongsTo
    {
        return $this->belongsTo(Boleto::class);
    }

    public function validador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validado_por');
    }
}
