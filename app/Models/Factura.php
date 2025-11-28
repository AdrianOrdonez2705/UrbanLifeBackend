<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;
    protected $table = 'factura';
    protected $primaryKey = 'id_factura';

    public $timestamps = false;

    protected $fillable = [
        'pedido_id_pedido',
        'contabilidad_id_contabilidad'
    ];
}
