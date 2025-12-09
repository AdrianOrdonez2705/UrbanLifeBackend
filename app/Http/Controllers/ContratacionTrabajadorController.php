<?php

namespace App\Http\Controllers;

use App\Models\ContratacionTrabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContratacionTrabajadorController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_trabajador' => 'required|integer',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'puesto' => 'required|string',
            'salario' => 'required|numeric',
            'contrato' => 'required|string',
            'activo' => 'required|boolean',
            'id_proyecto' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {

            $validatedData = $validator->validated();
            $contratacion = ContratacionTrabajador::create([
                'trabajador_id_trabajador' => $validatedData['id_trabajador'],
                'fecha_inicio'             => $validatedData['fecha_inicio'],
                'fecha_fin'                => $validatedData['fecha_fin'],
                'puesto'                   => $validatedData['puesto'],
                'salario'                  => $validatedData['salario'],
                'contrato'                 => $validatedData['contrato'],
                'activo'                   => $validatedData['activo'],
                'proyecto_id_proyecto'     => $validatedData['id_proyecto']
            ]);

            if (!$contratacion) {
                throw new \Exception('Error al crear la contratación');
            }

            return response()->json([
                'message' => 'Contratacion registrada correctamente',
                'data' => $contratacion
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo crear la contratacion',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
