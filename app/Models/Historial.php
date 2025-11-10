<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'HISTORIAL';
    protected $primaryKey = 'COD_HIS';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'COD_PAC',
        'FEC_CRE_HIS',
        'ANT_PER_HIS',
        'ANT_FAM_HIS',
        'ALE_HIS',
        'TRA_PRE_HIS',
        'OBS_GEN_HIS',
        'HAB_ALI_HIS',
        'PES_HIS',
        'ALT_HIS',
        'TIP_SAN_HIS'
    ];

    // Generar código automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Historial::orderBy('COD_HIS','desc')->first();
            $num = $last ? intval(substr($last->COD_HIS, 3)) + 1 : 1;
            $model->COD_HIS = 'HIS' . str_pad($num, 2, '0', STR_PAD_LEFT);
        });
    }

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'COD_PAC', 'COD_PAC');
    }
}
