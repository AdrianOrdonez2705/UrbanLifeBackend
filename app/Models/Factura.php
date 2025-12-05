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

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id_pedido', 'id_pedido');
    }

    public function contabilidad()
    {
        return $this->belongsTo(Contabilidad::class, 'contabilidad_id_contabilidad', 'id_contabilidad');
    }

}
