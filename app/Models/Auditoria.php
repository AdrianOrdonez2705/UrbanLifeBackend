<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;
    protected $table = 'auditoria';
    protected $primaryKey = 'id_login';
    public $timestamps = false;
    protected $fillable = [
        'hora',
        'fecha',
        'id_usuario',
        'status',
    ];
}