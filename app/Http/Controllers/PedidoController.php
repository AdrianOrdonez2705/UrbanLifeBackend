<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Http\Resources\PedidoResource;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function getMaterialPedidos()
    {
        try {
            $pedidos = Pedido::with([
                'proveedor:id_proveedor,nombre',
                'materiales:id_material,material'
            ])
            ->where('estado', 'pendiente')
            ->get();

            return PedidoResource::collection($pedidos);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los pedidos y materiales.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}