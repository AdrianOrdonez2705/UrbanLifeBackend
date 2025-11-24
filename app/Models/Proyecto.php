<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    use HasFactory;
    protected $table = 'proyecto';
    protected $primaryKey = 'id_proyecto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'imagen',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'presupuesto',
        'departamento'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    public function contrataciones() : HasMany {
        return $this->hasMany(ContratacionTrabajador::class, 'proyecto_id_proyecto', 'id_proyecto');
    }

    public function asistencias() : HasMany {
        return $this->hasMany(Asistencia::class, 'proyecto_id_proyecto', 'id_proyecto');
    }
}
