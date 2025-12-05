<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;
    protected $table = 'proveedor';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'correo',
        'direccion',
        'visibilidad',
        'logo',
        'web'
    ];

    protected $casts = [
        'visibilidad' => 'boolean',
    ];

    public function materiales() : HasMany {
        return $this->hasMany(MaterialProveedor::class, 'proveedor_id_proveedor', 'id_proveedor');
    }

    public function pedidos() : HasMany {
        return $this->hasMany(Pedido::class, 'proveedor_id_proveedor', 'id_proveedor');
    }
}
