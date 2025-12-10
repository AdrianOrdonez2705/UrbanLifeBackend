<?php

namespace App\Http\Controllers;

use App\Models\ImagenActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class ImagenActividadController extends Controller
{
    public function store(Request $request) : JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'id_actividad' => 'required|integer',
            'nombre' => 'sometimes|string',
            'tipo' => 'sometimes|string',
            'fecha' => 'sometimes|date',
            'ruta' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        $imagenActividad = ImagenActividad::create([
            'actividad_id_actividad' => $data['id_actividad'],
            'nombre' => Arr::get($data, 'nombre', null),
            'tipo' => Arr::get($data, 'tipo', null),
            'fecha' => Arr::get($data, 'fecha', null),
            'ruta' => $data['ruta']
        ]);

        return response()->json($imagenActividad, 201);
    }
}
