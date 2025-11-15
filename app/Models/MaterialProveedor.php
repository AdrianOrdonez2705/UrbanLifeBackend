<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialProveedor extends Model
{
    use HasFactory;
    
    protected $table = 'material_proveedor';
    protected $primaryKey = 'id_material';
    public $timestamps = false;
    protected $fillable = [
        'proveedor_id_proveedor',
        'material',
        'descripcion'
    ];

    protected $casts = [
        'proveedor_id_proveedor' => 'integer'
    ];

    public function proveedor() : BelongsTo {
        return $this->belongsTo(Proveedor::class, 'proveedor_id_proveedor', 'id_proveedor');
    }
}
