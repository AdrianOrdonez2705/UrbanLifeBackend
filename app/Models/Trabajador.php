<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajador';
    protected $primaryKey = 'id_trabajador';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_nac',
        'experiencia'
    ];

    protected $casts = [
        'fecha_nac' => 'date',
    ];

    public function contrataciones() : HasMany {
        return $this->hasMany(ContratacionTrabajador::class, 'trabajador_id_trabajador', 'id_trabajador');
    }

    public function asistencias() : HasMany {
        return $this->hasMany(Asistencia::class, 'trabajador_id_trabajador', 'id_trabajador');
    }
}
