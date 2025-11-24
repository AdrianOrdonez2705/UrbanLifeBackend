<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\ContratacionTrabajador;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrabajadorController extends Controller
{
    public function index() {
        $trabajadores = Trabajador::all();
        return response()->json($trabajadores);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'fecha_nac' => 'required|date',
            'experiencia' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $trabajador = Trabajador::create($validator->validated());
        return response()->json($trabajador, 201);
    }

    public function update(Request $request, $id_trabajador) {

        $trabajador = Trabajador::find($id_trabajador);

        if (!$trabajador) {
            return response()->json(['message' => 'Trabajador no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'fecha_nac' => 'sometimes|required|date',
            'experiencia' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $trabajador->update($validator->validated());

        return response()->json($trabajador);
    }

    public function getDetallesTrabajador($id_trabajador)
    {
        $trabajador = Trabajador::find($id_trabajador);

        if (!$trabajador) {
            return response()->json(['message' => 'Trabajador no encontrado'], 404);
        }

        $ultimaContratacion = ContratacionTrabajador::where('trabajador_id_trabajador', $id_trabajador)
            ->orderBy('fecha_inicio', 'desc')
            ->first();

        $proyecto = null;
        if ($ultimaContratacion) {
            $proyecto = $ultimaContratacion->proyecto;
        }

        $ultimaAsistencia = Asistencia::where('trabajador_id_trabajador', $id_trabajador)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_entrada', 'desc')
            ->first();

        $datos_respuesta = [
            'id_trabajador' => $trabajador->id_trabajador,
            'nombre_trabajador' => $trabajador->nombre,
            'puesto' => $ultimaContratacion ? $ultimaContratacion->puesto : null,
            'fecha_inicio' => $ultimaContratacion ? $ultimaContratacion->fecha_inicio : null,
            'fecha_fin' => $ultimaContratacion ? $ultimaContratacion->fecha_fin : null,
            'salario' => $ultimaContratacion ? $ultimaContratacion->salario : null,
            'id_proyecto' => $proyecto ? $proyecto->id_proyecto : null,
            'nombre_proyecto' => $proyecto ? $proyecto->nombre : null,
            'observacion' => $ultimaAsistencia ? $ultimaAsistencia->observacion : null,
            'fecha' => $ultimaAsistencia ? $ultimaAsistencia->fecha : null,
            'hora_entrada' => $ultimaAsistencia ? $ultimaAsistencia->hora_entrada : null,
        ];

        return response()->json($datos_respuesta);
    }
}
