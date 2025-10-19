<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prueba extends Model
{
    protected $table = "prueba";
    protected $keyType = "int";
    public $incrementing = true;

    protected $fillable = ['nombre'];
    public $timestamps = false;
}
