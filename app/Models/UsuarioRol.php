<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioRol extends Model
{
    protected $table = 'USUARIO_ROL';
    protected $primaryKey = 'ID_URO';
    public $timestamps = false;

    protected $fillable = [
        'ID_USU_URO',
        'ID_ROL_URO',
        'EST_URO',
        'FEC_URO',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_USU_URO', 'ID_USU');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'ID_ROL_URO', 'ID_ROL');
    }
}
