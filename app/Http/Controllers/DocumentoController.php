<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Prompts\Concerns\Fallback;

class DocumentoController extends Controller
{
    public function store(Request $request) : JsonResponse 
    {
        try {
            $validatedData = $request->validate([
                'id_proyecto' => 'required|integer|exists:proyecto,id_proyecto',
                'nombre_documento' => 'required|string',
                'tipo' => 'required|string',
                'fecha' => 'required|date',
                'ruta' => 'required|string'
            ]);

            $mappedData = [
                'proyecto_id_proyecto' => $validatedData['id_proyecto'],
                'nombre' => $validatedData['nombre_documento'],
                'tipo' => $validatedData['tipo'],
                'fecha' => $validatedData['fecha'],
                'ruta' => $validatedData['ruta']
            ];

            $documento = Documento::create($mappedData);

            return response()->json([
                'success' => true,
                'message' => 'Documento registrado existosamente',
                'data' => $documento
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al registrar el documento' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
