<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacora';
    protected $primaryKey = 'cod_bit';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cod_usu',
        'acc_bit',
        'fec_hor_bit',
    ];

    // Generar automáticamente BIT001, BIT002, ...
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Bitacora::orderBy('cod_bit', 'desc')->first();
            $num = $last ? intval(substr($last->cod_bit, 3)) + 1 : 1;
            $model->cod_bit = 'BIT' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });
    }

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usu', 'cod_usu');
    }
}
