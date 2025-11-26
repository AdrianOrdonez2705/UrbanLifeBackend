<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialPedido extends Model
{
    use HasFactory;

    protected $table = 'material_pedido';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'id_material',
        'cantidad',
        'precio_unitario'
    ];

    public function pedido() 
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');    
    }

    public function materialProveedor(): BelongsTo
    {
        return $this->belongsTo(MaterialProveedor::class, 'id_material', 'id_material');
    }
}
