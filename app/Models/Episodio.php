<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episodio extends Model
{
    use HasFactory;

    protected $table = 'EPISODIO';
    protected $primaryKey = 'COD_EPI';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'COD_PAC',
        'COD_MED',
        'FEC_INI_EPI',
        'FEC_FIN_EPI',
        'DUR_EPI',
        'INT_EPI',
        'DES_EPI',
        'EST_EMO_EPI',
        'INT_MED_EPI',
        'HOSP_EPI',
        'OBS_CLI_EPI'
    ];

    // Generar código automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Episodio::orderBy('COD_EPI','desc')->first();
            $num = $last ? intval(substr($last->COD_EPI, 3)) + 1 : 1;
            $model->COD_EPI = 'EPI' . str_pad($num, 2, '0', STR_PAD_LEFT);
        });
    }

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'COD_PAC', 'COD_PAC');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'COD_MED', 'COD_MED');
    }

    public function recetarios()
    {
        return $this->hasMany(Recetario::class, 'COD_EPI', 'COD_EPI');
    }
}
