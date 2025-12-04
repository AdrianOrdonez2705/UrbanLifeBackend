<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class Documento extends Model
{
    use HasFactory;
    protected $table = 'documento';
    protected $primaryKey = 'id_documento';
    public $timestamps = false;

    protected $fillable = [
        'proyecto_id_proyecto',
        'nombre',
        'tipo',
        'fecha',
        'ruta'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];
}
