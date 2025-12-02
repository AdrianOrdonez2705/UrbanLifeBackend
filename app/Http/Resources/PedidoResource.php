<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pedido' => $this->id_pedido,
            'nombre_proveedor' => $this->proveedor->nombre, 
            'fecha_solicitud' => $this->fecha_solicitud, 
            'materiales' => MaterialesPedidoResource::collection($this->materiales),
            'id_proyecto'=> $this->id_proyecto,
        ];
    }
}