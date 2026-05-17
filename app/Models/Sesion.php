<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sesion extends Model
{
    protected $table = 'SESION';
    protected $primaryKey = 'ID_SES';
    public $timestamps = false;

    protected $fillable = [
        'ID_USU_SES',
        'TOK_SES',
        'DIS_SES',
        'EXP_SES',
        'FEC_SES',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_USU_SES', 'ID_USU');
    }
}
