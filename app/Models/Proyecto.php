<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    use HasFactory;
    protected $table = 'proyecto';
    protected $primaryKey = 'id_proyecto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'presupuesto',
        'departamento',
        'id_usuario',
        'id_empleado'
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

    public function movimientosContables() : HasMany {
        return $this->hasMany(Contabilidad::class, 'proyecto_id_proyecto', 'id_proyecto');
    }

    public function pedidos() : HasMany {
        return $this->hasMany(Pedido::class, 'id_proyecto', 'id_proyecto');
    }

    public function documentos() : HasMany 
    {
        return $this->hasMany(Documento::class, 'proyecto_id_proyecto', 'id_proyecto');    
    }

    public function actividades() : HasMany 
    {
        return $this->hasMany(Actividad::class, 'proyecto_id_proyecto', 'id_proyecto');
    }

    public function empleado() : BelongsTo 
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');    
    }
}
