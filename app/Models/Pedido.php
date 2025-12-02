<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Pedido extends Model
{
    use HasFactory;
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor',
        'fecha_solicitud',
        'estado',
        'id_proyecto',
        'fecha_llegada_estimada',
        'fecha_llegada_real',     
        'mensaje',                
    ];

    public function proveedor(): BelongsTo {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function materiales() : BelongsToMany {
        return $this->belongsToMany(
            MaterialProveedor::class,
            'material_pedido',
            'id_pedido',
            'id_material'
        )->withPivot('cantidad', 'precio_unitario');
    }

    public function materiales_pedido()
    {
        return $this->hasMany(MaterialPedido::class, 'id_pedido', 'id_pedido');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id_proyecto');
    }

    public function movimientosContables(): HasManyThrough
    {
        return $this->hasManyThrough(
            Contabilidad::class, 
            Proyecto::class, 
            'id_proyecto',
            'proyecto_id_proyecto',
            'id_proyecto',
            'id_proyecto'
        );
    }

    protected function monto(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('movimientosContables')) {
                    return $this->movimientosContables->sum('monto');
                }
                
                return $this->movimientosContables()->sum('monto');
            }
        );
    }
}