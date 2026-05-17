<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $table = 'CLIENTE';
    protected $primaryKey = 'ID_CLI';
    public $timestamps = false;

    protected $fillable = [
        'ID_USU_CLI',
        'NOM_CLI',
        'APE_CLI',
        'CED_CLI',
        'EMA_CLI',
        'TEL_CLI',
        'FEC_NAC_CLI',
        'EST_CLI',
        'REG_CLI',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_USU_CLI', 'ID_USU');
    }
}
