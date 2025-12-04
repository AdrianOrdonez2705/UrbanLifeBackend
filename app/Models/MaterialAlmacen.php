<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialAlmacen extends Model
{
    use HasFactory;
    protected $table = 'material_almacen';
    protected $primaryKey = 'id_material_almacen';
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor',
        'nombre',
        'cantidad'
    ];

    protected $casts = [
        'cantidad' => 'float'
    ];
}