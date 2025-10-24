<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    protected $table = 'mensaje';
    protected $primaryKey = 'id_correo';
    protected $fillable = [
        'nombre',
        'correo',
        'mensaje',
    ];
    public $timestamps = false;
}
