<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialesPedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'material' => $this->material,
            'cantidad' => (int) $this->pivot->cantidad,
            'precio_unitario' => (float) $this->pivot->precio_unitario,
        ];
    }
}