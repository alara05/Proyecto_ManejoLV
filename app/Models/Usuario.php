<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Usuario extends Model
{
    protected $table = 'USUARIO';
    protected $primaryKey = 'ID_USU';
    public $timestamps = false;

    protected $fillable = [
        'NOM_USU',
        'APE_USU',
        'CED_USU',
        'EMA_USU',
        'TEL_USU',
        'CLA_USU',
        'EST_USU',
        'FEC_USU',
        'ACT_USU',
    ];

    protected $hidden = [
        'CLA_USU',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'USUARIO_ROL',
            'ID_USU_URO',
            'ID_ROL_URO',
            'ID_USU',
            'ID_ROL'
        )->withPivot(['ID_URO', 'EST_URO', 'FEC_URO']);
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'ID_USU_CLI', 'ID_USU');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class, 'ID_USU_SES', 'ID_USU');
    }
}
