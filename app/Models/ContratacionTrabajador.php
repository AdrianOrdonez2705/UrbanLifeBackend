<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratacionTrabajador extends Model
{
    use HasFactory;

    protected $table = 'contratacion_trabajador';
    protected $primaryKey = 'id_contratacion_trabajador';
    public $timestamps = false;

    protected $fillable = [
        'trabajador_id_trabajador',
        'proyecto_id_proyecto',
        'fecha_inicio',
        'fecha_fin',
        'puesto',
        'salario',
        'contrato',
        'observacion',
        'activo'
    ];
    
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'salario' => 'float',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id_trabajador', 'id_trabajador');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id_proyecto', 'id_proyecto');
    }
}