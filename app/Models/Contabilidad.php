<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contabilidad extends Model
{
    use HasFactory;

    protected $table = 'contabilidad';
    protected $primaryKey = 'id_contabilidad';
    public $timestamps = false;

    protected $fillable = [
        'proyecto_id_proyecto',
        'movimiento',
        'descripcion',
        'monto',
        'fecha',
        'tipo'
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha' => 'date'
    ];

    public function proyecto() : BelongsTo {
        return $this->belongsTo(Proyecto::class, 'proyecto_id_proyecto', 'id_proyecto');
    }
}
