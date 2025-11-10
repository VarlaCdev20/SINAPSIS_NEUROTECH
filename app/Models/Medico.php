<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $table = 'medico'; // 👈 asegúrate que coincida con la tabla
    protected $primaryKey = 'cod_med';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['cod_usu'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Medico::orderBy('cod_med', 'desc')->first();
            $num = $last ? intval(substr($last->cod_med, 3)) + 1 : 1;
            $model->cod_med = 'MED' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usu', 'cod_usu');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'cod_med', 'cod_med');
    }

    public function episodios()
    {
        return $this->hasMany(Episodio::class, 'cod_med', 'cod_med');
    }

    public function recetarios()
    {
        return $this->hasMany(Recetario::class, 'cod_med', 'cod_med');
    }
}
