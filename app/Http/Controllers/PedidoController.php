<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Http\Resources\PedidoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request): JsonResponse 
    {
        $rules = [
            'id_proveedor' => ['required', 'integer'],
            'fecha_solicitud' => ['required', 'date_format:Y-m-d'],
            'estado' => ['required', 'string', 'max:50'],
            'id_proyecto' => ['required', 'integer'],
            'materiales' => ['required', 'array', 'min:1'],
            'materiales.*.id_material_proveedor' => ['required', 'integer'],
            'materiales.*.cantidad' => ['required', 'integer', 'min:1'],
            'materiales.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de la solicitud',
                'errores' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $datosPedido = $request->only([
                'id_proveedor',
                'fecha_solicitud',
                'estado',
                'id_proyecto',
            ]);

            $pedido = Pedido::create($datosPedido);
            
            $idPedido = $pedido->id_pedido;

            $materiales = $request->input('materiales');

            $datosMateriales = [];
            foreach ($materiales as $material) {
                $datosMateriales[] = [
                    'id_pedido' => $idPedido,
                    'id_material' => $material['id_material_proveedor'],
                    'cantidad' => $material['cantidad'],
                    'precio_unitario' => $material['precio_unitario'],
                ];
            }

            $pedido->materiales_pedido()->createMany($datosMateriales);

            DB::commit();

            return response()->json([
                'mensaje' => 'Pedido y materiales guardados exitosamente.',
                'id_pedido' => $idPedido,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'mensaje' => 'Error al guardar el pedido. La operación fue revertida.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}