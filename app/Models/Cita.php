<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'cita';
    protected $primaryKey = 'cod_cit';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'cod_cit',
        'cod_sol',
        'cod_med',
        'cod_pac',
        'tip_cit',
        'mot_cit',
        'est_cit',
        'rep_cit',
        'fec_cit',
        'fec_reg_cit',
    ];

    // 🔹 Generar código automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->cod_cit)) {
                $last = Cita::orderBy('cod_cit', 'desc')->first();
                $num = $last ? intval(substr($last->cod_cit, 3)) + 1 : 1;
                $model->cod_cit = 'CIT' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relaciones coherentes con tus migraciones
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class, 'cod_sol', 'cod_sol');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'cod_med', 'cod_med');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'cod_pac', 'cod_pac');
    }
}
