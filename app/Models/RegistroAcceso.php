<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroAcceso extends Model
{
    protected $fillable = [
        'boleto_id',
        'registrado_por',
        'registrado_at',
        'resultado',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'registrado_at' => 'datetime',
        ];
    }

    public function boleto(): BelongsTo
    {
        return $this->belongsTo(Boleto::class);
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
