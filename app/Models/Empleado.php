<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Empleado extends Model
{

    protected $table = 'empleado';
    protected $primaryKey = 'id_empleado';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'puesto',
        'contrato',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function actividades() : BelongsToMany 
    {
        return $this->belongsToMany(
            Actividad::class,
            'empleado_actividad',
            'empleado_id_empleado',
            'actividad_id_actividad'
        );
    }
}
