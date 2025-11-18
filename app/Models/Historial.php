<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'historial'; // 👉 minúsculas (tu tabla real)
    protected $primaryKey = 'cod_his'; // 👉 minúsculas (tu columna real)
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cod_pac',
        'fec_cre_his',
        'ant_per_his',
        'ant_fam_his',
        'ale_his',
        'tra_pre_his',
        'obs_gen_his',
        'hab_ali_his',
        'pes_his',
        'alt_his',
        'tip_san_his'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Historial::orderBy('cod_his', 'desc')->first();
            $num = $last ? intval(substr($last->cod_his, 3)) + 1 : 1;
            $model->cod_his = 'HIS' . str_pad($num, 2, '0', STR_PAD_LEFT);
        });
    }

    public function Paciente()
    {
        return $this->belongsTo(Paciente::class, 'cod_pac', 'cod_pac');
    }
}
