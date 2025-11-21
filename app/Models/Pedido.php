<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pedido extends Model
{
    use HasFactory;
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false;

    public function proveedor(): BelongsTo {
        return $this->belongsTo(Proveedor::class, 'proveedor_id_proveedor', 'id_proveedor');
    }

    public function materiales() : BelongsToMany {
        return $this->belongsToMany(
            MaterialProveedor::class,
            'material_pedido',
            'pedido_id_pedido',
            'material_id_material'
        )->withPivot('cantidad', 'precio_unitario');
    }
}
