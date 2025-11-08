<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = [
        'rol',
        'descripcion'
    ];

    public function usuarios() {
        return $this->hasMany(Usuario::class, 'rol_id_rol', 'id_rol');
    }
}
