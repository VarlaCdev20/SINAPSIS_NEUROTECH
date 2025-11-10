<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes'; // ✅ en minúscula
    protected $primaryKey = 'cod_pac';
    public $incrementing = false;
    protected $keyType = 'string';

    // ✅ Ahora maneja los tres campos principales
    protected $fillable = ['cod_usu', 'cod_med'];

    /**
     * 🔹 Genera automáticamente el código del paciente (PAC001, PAC002, ...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Paciente::orderBy('cod_pac', 'desc')->first();
            $num = $last ? intval(substr($last->cod_pac, 3)) + 1 : 1;
            $model->cod_pac = 'PAC' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });
    }

    // 🔸 Relación con la tabla users
    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usu', 'cod_usu');
    }

    // 🔸 Relación con el médico
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'cod_med', 'cod_med');
    }

    // 🔸 Relaciones adicionales
    public function citas()
    {
        return $this->hasMany(Cita::class, 'cod_pac', 'cod_pac');
    }

    public function episodios()
    {
        return $this->hasMany(Episodio::class, 'cod_pac', 'cod_pac');
    }

    public function recetarios()
    {
        return $this->hasMany(Recetario::class, 'cod_pac', 'cod_pac');
    }
}
