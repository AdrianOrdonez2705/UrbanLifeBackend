<?php

namespace App\Http\Controllers;

use App\Http\Resources\PedidoMaterialResource;
use App\Http\Resources\PedidoResource;
use App\Models\Contabilidad;
use App\Models\Factura;
use App\Models\Pedido;
use Illuminate\Cache\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

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

    public function index(): JsonResponse
    {
        try {
            $pedidos = Pedido::with([
                'proveedor:id_proveedor,nombre',
                'proyecto:id_proyecto,nombre',
                'movimientosContables:proyecto_id_proyecto,monto',
                'materiales_pedido.materialProveedor:id_material,material'
            ])
                ->get();

            $recursos = PedidoMaterialResource::collection($pedidos);

            return response()->json([
                'mensaje' => 'Lista de pedidos recuperada exitosamente.',
                'data' => $recursos->toArray(request())
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al obtener los pedidos y materiales.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pedidoAceptado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pedido' => 'required|integer',
            'id_proyecto' => 'required|integer',
            'movimiento' => 'required|string',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'monto' => 'required|numeric',
            'tipo' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        $id_pedido = $validatedData['id_pedido'];
        $dataContabilidad = $validatedData;

        $dataContabilidad['proyecto_id_proyecto'] = $dataContabilidad['id_proyecto'];
        unset($dataContabilidad['id_proyecto']);

        $contabilidad = Contabilidad::create($dataContabilidad);
        $id_contabilidad = $contabilidad->id_contabilidad;

        $pedido = Pedido::find($id_pedido);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        if ($pedido->estado == 'aceptado') {
            return response()->json(['message' => 'Este pedido ya fue aceptado antes'], 422);
        }

        $pedido->estado = 'aceptado';
        $pedido->save();

        $dataFactura = Factura::create([
            'pedido_id_pedido' => $id_pedido,
            'contabilidad_id_contabilidad' => $id_contabilidad
        ]);

        return response()->json([
            'message' => 'Estado de pedido actualizado, contabilidad y factura registrados exitosamente',
            'contabilidad' => $contabilidad,
            'factura' => $dataFactura
        ], 201);
    }
}
