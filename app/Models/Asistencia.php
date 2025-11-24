<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencia';
    protected $primaryKey = 'id_asistencia';
    public $timestamps = false;

    protected $fillable = [
        'proyecto_id_proyecto',
        'trabajador_id_trabajador',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
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