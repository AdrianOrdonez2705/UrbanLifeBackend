<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    use HasFactory;
    protected $table = 'actividad';
    protected $primaryKey = 'id_actividad';
    public $timestamps = false;

    protected $fillable = [
        'contrato_id_contrato',
        'nombre',
        'descripcion',
        'fecha',
        'estado',
        'proyecto_id_proyecto'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function imagenes() : HasMany 
    {
        return $this->hasMany(ImagenActividad::class, 'actividad_id_actividad', 'id_actividad');    
    }

    public function empleados() : BelongsToMany 
    {
        return $this->belongsToMany(
            Empleado::class,
            'empleado_actividad',
            'actividad_id_actividad',
            'empleado_id_empleado'
        );    
    }
}
