<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagenActividad extends Model
{
    use HasFactory;
    protected $table = 'imagen_actividad';
    protected $primaryKey = 'id_imagen';
    public $timestamps = false;

    protected $fillable = [
        'actividad_id_actividad',
        'nombre',
        'tipo',
        'fecha',
        'ruta'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function actividad() : BelongsTo 
    {
        return $this->belongsTo(Actividad::class, 'actividad_id_actividad', 'id_actividad');    
    }
}
