<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoMaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pedido' => $this->id_pedido,
            'id_proveedor' => $this->id_proveedor,
            'nombre_proveedor' => $this->whenLoaded('proveedor', function () {
                return $this->proveedor->nombre ?? null; 
            }),
            'fecha_solicitud' => $this->fecha_solicitud,
            'fecha_llegada_estimada' => $this->fecha_llegada_estimada,
            'fecha_llegada_real' => $this->fecha_llegada_real,
            'estado' => $this->estado,
            'mensaje' => $this->mensaje,
            'monto' => $this->monto, 
            'materiales' => $this->whenLoaded('materiales_pedido', function () {
                return $this->materiales_pedido
                    ->filter(fn ($materialPedido) => $materialPedido->relationLoaded('materialProveedor'))
                    ->map(function ($materialPedido) {
                        return [
                            'id_material' => $materialPedido->id_material,
                            'material' => $materialPedido->materialProveedor->material ?? 'Material Desconocido',
                            'cantidad' => $materialPedido->cantidad,
                        ];
                    })
                    ->values(); 
            }),
        ];
    }
}
