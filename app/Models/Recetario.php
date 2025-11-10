<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recetario extends Model
{
    use HasFactory;

    protected $table = 'RECETARIO';
    protected $primaryKey = 'COD_REC';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'COD_PAC',
        'COD_MED',
        'COD_EPI',
        'TIT_REC',
        'DES_REC',
        'DIA_REC',
        'FEC_EMI_REC'
    ];

    // Generar código automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Recetario::orderBy('COD_REC','desc')->first();
            $num = $last ? intval(substr($last->COD_REC, 3)) + 1 : 1;
            $model->COD_REC = 'REC' . str_pad($num, 2, '0', STR_PAD_LEFT);
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

    public function episodio()
    {
        return $this->belongsTo(Episodio::class, 'COD_EPI', 'COD_EPI');
    }
}
