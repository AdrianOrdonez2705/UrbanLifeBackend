<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialProyecto extends Model
{
    use HasFactory;
    protected $table = 'material_proyecto';
    protected $primaryKey = 'id_entrega';
    public $timestamps = false;

    protected $fillable = [
        'material_id_material',
        'proyecto_id_proyecto',
        'fecha_entrega',
        'cantidad'
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'cantidad' => 'float'
    ];
}
