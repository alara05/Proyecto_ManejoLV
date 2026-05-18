<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionAplicacion extends Model
{
    protected $table = 'configuracion_aplicacion';

    protected $fillable = [
        'nombre_aplicacion',
        'logo_path',
        'color_primario',
        'color_secundario',
        'email_soporte',
        'telefono_soporte',
        'redes_sociales',
    ];

    protected function casts(): array
    {
        return [
            'redes_sociales' => 'array',
        ];
    }
}
