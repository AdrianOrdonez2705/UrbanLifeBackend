<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoActividad extends Model
{
    use HasFactory;
    protected $table = 'empleado_actividad';
    protected $primaryKey = 'id_empleado_actividad';
    public $timestamps = false;
    protected $fillable = [
        'empleado_id_empleado',
        'actividad_id_actividad'
    ];
}
