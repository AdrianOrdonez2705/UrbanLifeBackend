<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Contabilidad;
use Illuminate\Http\Request;

class ContabilidadController extends Controller
{
    public function getAllMovimientos()
    {
        $proyectos = Proyecto::with('movimientosContables')->get();

        if ($proyectos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron proyectos o movimientos.'], 404);
        }

        $datos_respuesta = $proyectos->map(function ($proyecto) {
            $movimientos_formateados = $proyecto->movimientosContables->map(function ($movimiento) {
                return [
                    'id_contabilidad' => $movimiento->id_contabilidad,
                    'movimiento' => $movimiento->movimiento,
                    'fecha' => $movimiento->fecha ? $movimiento->fecha->format('Y-m-d') : null,
                    'tipo' => $movimiento->tipo,
                    'monto' => $movimiento->monto,
                    'descripcion' => $movimiento->descripcion,
                ];
            })->toArray();

            return [
                'id_proyecto' => $proyecto->id_proyecto,
                'nombre_proyecto' => $proyecto->nombre,
                'movimientos' => $movimientos_formateados,
            ];
        })->toArray();

        return response()->json($datos_respuesta);
    }

    public function getMovimientosConPresupuesto()
    {
        $proyectos = Proyecto::with('movimientosContables')->get();

        if ($proyectos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron proyectos o movimientos.'], 404);
        }

        $datos_respuesta = $proyectos->map(function ($proyecto) {
            $movimientos_formateados = $proyecto->movimientosContables->map(function ($movimiento) {
                return [
                    'id_contabilidad' => $movimiento->id_contabilidad,
                    'movimiento' => $movimiento->movimiento,
                    'monto' => $movimiento->monto,
                ];
            })->toArray();

            return [
                'id_proyecto' => $proyecto->id_proyecto,
                'nombre_proyecto' => $proyecto->nombre,
                'presupuesto' => $proyecto->presupuesto,
                'movimientos' => $movimientos_formateados,
            ];
        })->toArray();

        return response()->json($datos_respuesta);
    }
}
