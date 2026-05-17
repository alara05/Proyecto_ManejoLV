<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    protected $table = 'ROL';
    protected $primaryKey = 'ID_ROL';
    public $timestamps = false;

    protected $fillable = [
        'NOM_ROL',
        'DES_ROL',
        'EST_ROL',
        'FEC_ROL',
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'USUARIO_ROL',
            'ID_ROL_URO',
            'ID_USU_URO',
            'ID_ROL',
            'ID_USU'
        )->withPivot(['ID_URO', 'EST_URO', 'FEC_URO']);
    }
}
