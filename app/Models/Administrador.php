<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    use HasFactory;

    protected $table = 'administrador'; // 👈 asegúrate que sea igual al nombre real de la tabla
    protected $primaryKey = 'cod_adm';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['cod_usu'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Administrador::orderBy('cod_adm', 'desc')->first();
            $num = $last ? intval(substr($last->cod_adm, 3)) + 1 : 1;
            $model->cod_adm = 'ADM' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usu', 'cod_usu');
    }
}
