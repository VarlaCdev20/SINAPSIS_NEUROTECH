<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitante extends Model
{
    use HasFactory;

    protected $table = 'solicitantes';
    protected $primaryKey = 'cod_sol';
    public $incrementing = false;
    protected $keyType = 'string';

    // Si tu tabla NO tiene created_at/updated_at, descomenta:
    // public $timestamps = false;

    protected $fillable = [
        'cod_sol',
        'nom_sol',
        'ap_pat_sol',
        'ap_mat_sol',
        'fec_nac_sol',
        'est_sol',
        'email_sol',
        'cel_sol',
        'dir_sol',     // ← (una sola vez)
        'ci_sol',
        'des_sol',
    ];

    protected $casts = [
        'fec_nac_sol' => 'date',
    ];

    /**
     * Relación: un solicitante puede tener muchas citas.
     * NOTA: En tu BD las columnas de Cita parecen estar en MAYÚSCULAS (p.ej. COD_CIT, FEC_REG_CIT),
     * por eso usamos 'COD_SOL' como foreign key en la tabla 'citas'.
     */
    public function citas()
    {
        // foreign key en la tabla 'citas' = 'COD_SOL'
        // local key en 'solicitantes' = 'cod_sol'
        return $this->hasMany(Cita::class, 'cod_sol', 'cod_sol');
    }

    /** Accesor: nombre completo listo para mostrar */
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nom_sol} {$this->ap_pat_sol} {$this->ap_mat_sol}");
    }

    /** Scope: sólo los que no tienen ninguna cita (pendientes) */
    public function scopePendientes($query)
    {
        return $query->whereDoesntHave('citas');
    }
}
