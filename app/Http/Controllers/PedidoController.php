<?php

namespace App\Http\Controllers;

use App\Http\Resources\PedidoMaterialResource;
use App\Http\Resources\PedidoResource;
use App\Models\Contabilidad;
use App\Models\Factura;
use App\Models\MaterialAlmacen;
use App\Models\Pedido;
use App\Models\Proyecto;
use Dotenv\Parser\Value;
use Illuminate\Cache\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Mail\PedidoMailable;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\EnviarPedido;

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

        $materiales = $pedido->materiales_pedido;
        $date = date('d-m-Y');
        $total = $materiales->sum(fn($item) => $item->cantidad * $item->precio_unitario);

        $data = [
            'date' => $date,
            'materiales' => $materiales,
            'proveedor' => $pedido->proveedor->nombre,
            'pedido' => $pedido,
            'total' => $total,
        ];

        $pdf = Pdf::loadView('pdf.generarPedidoPDF', $data);

        Mail::to($pedido->proveedor->correo)->send(new EnviarPedido($pedido, $pedido->proveedor->nombre, $total, $date, $pdf));


        return response()->json([
            'message' => 'Pedido aceptado, contabilidad y factura registrados, PDF enviado al proveedor',
            'contabilidad' => $contabilidad,
            'factura' => $dataFactura
        ], 201);

    }

    public function pedidoTransito(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pedido' => 'required|integer',
            'fecha_llegada_estimada' => 'required|date',
            'estado' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        $id_pedido = $data['id_pedido'];

        $pedido = Pedido::find($id_pedido);

        if (!$pedido) {
            return response()->json(['message' => 'No se pudo encontrar el pedido'], 404);
        }

        $pedido->fecha_llegada_estimada = $data['fecha_llegada_estimada'];
        $pedido->estado = $data['estado'];
        $pedido->save();

        return response()->json([
            'message' => 'Estado del pedido (transito) actualizado exitosamente',
            'data' => $pedido
        ], 200);
    }

    public function pedidoRechazado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pedido' => 'required|integer',
            'estado' => 'required|string',
            'mensaje' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $id_pedido = $data['id_pedido'];

        $pedido = Pedido::find($id_pedido);

        if (!$pedido) {
            return response()->json(['message' => 'No se pudo encontrar el pedido'], 404);
        }

        $pedido->estado = $data['estado'];
        $pedido->mensaje = $data['mensaje'];

        $pedido->save();

        return response()->json([
            'message' => 'Estado y mensaje actualizados, pedido rechazado exitosamente',
            'pedido' => $pedido
        ], 200);
    }

    public function pedidoRecibido(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'id_pedido' => 'required|integer',
            'id_proveedor' => 'required|integer',
            'estado' => 'required|string',
            'fecha_llegada_real' => 'required|date',
            'materiales' => 'required|array|min:1',
            'materiales.*.nombre' => 'required|string',
            'materiales.*.cantidad' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $id_pedido = $data['id_pedido'];
        $id_proveedor = $data['id_proveedor'];
        $materiales = $data['materiales'];
        $materialesCreados = [];

        DB::beginTransaction();

        try {
            $pedido = Pedido::find($id_pedido);

            if (!$pedido) {
                DB::rollBack();
                return response()->json(['message' => 'No se pudo encontrar el pedido'], 404);
            }
            
            if ($pedido->estado == 'recibido') {
                DB::rollBack();
                return response()->json(['message' => 'Este pedido ya había sido marcado como recibido'], 422);
            }

            $pedido->estado = $data['estado'];
            $pedido->fecha_llegada_real = $data['fecha_llegada_real'];
            $pedido->save();

            foreach ($materiales as $material) {

                $buscar = [
                    'nombre' => $material['nombre']
                ];

                $materialAlmacen = MaterialAlmacen::firstOrCreate(
                    $buscar,
                    [
                        'id_proveedor' => $id_proveedor,
                        'cantidad' => 0
                    ]
                );

                $materialAlmacen->increment('cantidad', $material['cantidad']);
                $materialAlmacen->update(['id_proveedor' => $id_proveedor]);
                $materialesCreados[] = $materialAlmacen->refresh();
            }

            DB::commit();

            return response()->json([
                'message' => 'Pedido marcado como recibido y materiales registrados en el almacén exitosamente.',
                'pedido' => $pedido,
                'materiales' => $materialesCreados
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar la recepción del pedido y registrar los materiales.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function contabilidadPorProyecto($idProyecto)
    {
        $proyecto = Proyecto::with(['pedidos.factura.contabilidad'])->findOrFail($idProyecto);

        $pedidos = $proyecto->pedidos;

        $totalProyecto = $pedidos->sum(function ($pedido) {
            return $pedido->factura && $pedido->factura->contabilidad
                ? $pedido->factura->contabilidad->monto
                : 0;
        });

        $data = [
            'proyecto' => $proyecto,
            'pedidos' => $pedidos,
            'totalProyecto' => $totalProyecto,
            'date' => date('Y-m-d'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.contabilidadProyecto', $data);

        return $pdf->download("ContabilidadProyecto_{$proyecto->id_proyecto}_{$data['date']}.pdf");
    }

}
