<?php

namespace App\Http\Controllers;

use App\Models\MaterialAlmacen;
use App\Models\MaterialProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class MaterialProyectoController extends Controller
{
    public function store(Request $request) : JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'id_material_almacen' => 'required|integer|exists:material_almacen,id_material_almacen',
            'id_proyecto' => 'required|integer',
            'fecha_entrega' => 'required|date',
            'cantidad' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $cantidadRequerida = $data['cantidad'];
        $idMaterial = $data['id_material_almacen'];

        $materialAlmacen = MaterialAlmacen::find($idMaterial);

        if (!$materialAlmacen || $materialAlmacen->cantidad < $cantidadRequerida) {
            return response()->json([
                'cantidad' => [
                    'La cantidad solicitada ' . $cantidadRequerida . ' excede el stock'
                ]
                ], 400);
        }

        try {
            DB::beginTransaction();

            $materialAlmacen->decrement('cantidad', $cantidadRequerida);

            $materialProyecto = MaterialProyecto::create([
                'material_id_material' => $idMaterial,
                'proyecto_id_proyecto' => $data['id_proyecto'],
                'fecha_entrega' => $data['fecha_entrega'],
                'cantidad' => $cantidadRequerida
            ]);

            DB::commit();

            return response()->json($materialProyecto, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error al asignar el material'
            ], 500);
        }
    }
}
