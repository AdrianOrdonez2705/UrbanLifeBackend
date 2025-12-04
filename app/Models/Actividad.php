<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
