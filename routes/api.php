<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\MensajeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\ContratacionTrabajadorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\MaterialProveedorController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\UsuarioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});

Route::get('/get_proyectos', [ProyectoController::class, 'index']);
Route::post('/mensaje', [MensajeController::class, 'store']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-2fa', [AuthController::class, 'verify2FA']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
});

// USUARIO
Route::post('/asignar_usuario', [UsuarioController::class, 'asignar_usuario']);
Route::get('/get_all_usuarios', [UsuarioController::class, 'index']);
Route::get('/get_usuario/id/{id_usuario}', [UsuarioController::class, 'show']);
Route::put('/update_usuario/{id_usuario}', [UsuarioController::class, 'update']);
Route::put('/eliminar_empleado/{id_empleado}', [UsuarioController::class, 'borradoLogico']);

// EMPLEADO
Route::post('/registrar_empleado', [EmpleadoController::class, 'register']);
Route::get('/get_all_empleados', [EmpleadoController::class, 'index']); // Este endpoint devuelve usuario true o false
Route::get('/get_empleado/id/{id_empleado}', [EmpleadoController::class, 'show']);
Route::put('/update_empleado/{id_empleado}', [EmpleadoController::class, 'update']);
Route::put('eliminar_empleado/{id_empleado}', [EmpleadoController::class, 'borradoLogico']);

// TRABAJADOR
Route::get('/get_all_trabajadores', [TrabajadorController::class, 'index']);
Route::post('/registrar_trabajador', [TrabajadorController::class, 'register']);
Route::put('/update_trabajador/{id_trabajador}', [TrabajadorController::class, 'update']);

// PROVEEDOR
Route::get('/get_proveedores', [ProveedorController::class, 'index']);
Route::post('/registrar_proveedor', [ProveedorController::class, 'register']);
Route::put('/update_proveedor/{id_proveedor}', [ProveedorController::class, 'update']);

// MATERIAL_PROVEEDOR
Route::get('/get_material_proveedores', [MaterialProveedorController::class, 'index']);
Route::post('/registrar_material_proveedor', [MaterialProveedorController::class, 'register']);
Route::put('/update_material_proveedor/{id_material}', [MaterialProveedorController::class, 'update']);

// PEDIDOS
Route::get('/get_material_pedidos', [PedidoController::class, 'getMaterialPedidos']);

// CONTABILIDAD
Route::get('/get_detalles_trabajadores', [TrabajadorController::class, 'getDetallesTrabajadores']);
Route::get('/get_movimientos_proyectos', [ContabilidadController::class, 'getAllMovimientos']);
Route::get('/get_movimientos_presupuesto', [ContabilidadController::class, 'getMovimientosConPresupuesto']);

// MATERIAL_PROVEEDOR
Route::get('/get_all_materiales_proveedores', [ProveedorController::class, 'getProveedoresWithMateriales']);

// PROYECTOS_ACTIVOS
Route::get('/get_proyectos_activos', [ProyectoController::class, 'getProyectosActivos']);

// REGISTRAR UN NUEVO PEDIDO CON ARREGLO DE MATERIALES
Route::post('/registrar_pedido', [PedidoController::class, 'store']);

// OBTENER TODOS LOS DATOS DE LOS PEDIDOS
Route::get('/get_all_pedidos', [PedidoController::class, 'index']);

// CAMBIAR ESTADO DE PEDIDO A "aceptado"
Route::post('/cambiar_aceptado', [PedidoController::class, 'pedidoAceptado']);

// CAMBIAR ESTADO DE PEDIDO A "transito"
Route::put('/cambiar_transito', [PedidoController::class, 'pedidoTransito']);

// CAMBIAR ESTADO DE PEDIDO A "rechazado"
Route::put('/cambiar_rechazado', [PedidoController::class, 'pedidoRechazado']);

// CAMBIAR ESTADO DE PEDIDO A "recibido"
Route::post('/cambiar_recibido', [PedidoController::class, 'pedidoRecibido']);



// PARA HU-006

// SACAR INFORMACION DE TODOS LOS PROYECTOS
Route::get('/get_all_proyectos_data', [ProyectoController::class, 'getAllProjectsData']);

// SACAR TODOS LOS USUARIOS DE TIPO JEFE DE OBRA
Route::get('/get_jefes_de_obra', [UsuarioController::class, 'getJefesDeObra']);


// REGISTRAR UN NUEVO PROYECTO
/*
    Este endpoint recibe un JSON así:
        {
            "nombre": "Lomas del Sol",
            "descripcion": "Condominio moderno en Achumani",
            "fecha_inicio": "2025-12-04",
            "fecha_fin": "2026-12-04",
            "estado": "en construcción",
            "presupuesto": 250000.0,
            "departamento": "La Paz",
            "id_usuario": 11,
            "id_empleado": 9
        }
*/
Route::post('/registrar_proyecto', [ProyectoController::class, 'store']);



// REGISTRAR UN DOCUMENTO
/*
    Este endpoint recibe un JSON así:
        {
            "id_proyecto": 4,
            "nombre_documento": "Planos iniciales",
            "tipo": "Planos",
            "fecha": "2025-12-04",
            "ruta": "https://ruta_a_documento"
        }
*/
Route::post('/registrar_documento', [DocumentoController::class, 'store']);



// PARA LOS DASHBOARDS
Route::get('/dashboard_data', [DashboardController::class, 'overview']);


// NUEVOS ENDPOINTS
Route::get('/index_trabajadores', [TrabajadorController::class, 'indexAll']);

/* Este endpoint recibe un json así:
    {
        "id_trabajador": 1,
        "fecha_inicio": "2025-12-08",
        "fecha_fin": "2026-12-08",
        "puesto": "Obrero",
        "salario": 2000,
        "contrato": "/ruta_a_contrato",
        "activo": true,
        "id_proyecto": 2
    }
*/
Route::post('/registrar_contratacion', [ContratacionTrabajadorController::class, 'store']);

Route::get('/get_all_contrataciones', [ContratacionTrabajadorController::class, 'index']);

/* Este endpoint recibe un JSON así:
    {
        "id_contratacion_trabajador": 1,
        "activo": false,
        "observacion": "No cumplió sus tareas encomendadas"
    }

# IMPORTANTE: Para este endpoint todos los campos son obligatorios ya que no pueden haber 
despidos injustificados.
*/
Route::put('/despedir_contratacion', [ContratacionTrabajadorController::class, 'activoFalsePorDespido']);

/* Este endpoint (PUT) recibe un JSON así:
    {
        "id_contratacion_trabajador": 1,
        "trabajador_id_trabajador": 2,
        "fecha_inicio": "2025-12-10",
        "fecha_fin": "2026-12-10",
        "puesto": "Ingeniero",
        "salario": 5000.0,
        "contrato": "/ruta_nueva",
        "activo": true,
        "proyecto_id_proyecto": 2
    }

# IMPORTANTE: Este endpoint tiene que recibir SÍ O SÍ el id_contratacion_trabajador
para poder ubicar el contrato para actualizar, además, en caso de que el JSON tenga
ya sea el id del trabajador o el id del proyecto, se lo tiene que mandar como
"trabajador_id_trabajador" y/o "proyecto_id_proyecto". Si se le manda solamente
como "id_trabajador" o "id_proyecto" puede que no funcione y retorne error.
*/
Route::put('/actualizar_contratacion', [ContratacionTrabajadorController::class, 'update']);

/* Este endpoint recibe un JSON así:
    {
        "contrato_id_contrato": 1,
        "nombre": "Sentar el tinglado",
        "descripcion": "Limpiar el ambiente para hacer el tinglado",
        "fecha": "2025-12-09",
        "estado": "en progreso",
        "proyecto_id_proyecto": 2,
        "empleados": [
            {
                "id_empleado": 1
            },
            {
                "id_empleado": 2
            }
        ]
    }
*/
Route::post('/registrar_actividad', [ActividadController::class, 'store']);

Route::get('/listar_actividades', [ActividadController::class, 'index']);

/* Este endpoint recibe un JSON así: 
    {
        "id_actividad": 1,
        "estado": "en progreso"
    }
*/
Route::put('/actividad_enprogreso', [ActividadController::class, 'cambiarEnProgreso']);

/* Este endpoint recibe un JSON así: 
    {
        "id_actividad": 1,
        "estado": "finalizado"
    }
*/
Route::put('/actividad_finalizado', [ActividadController::class, 'cambiarFinalizado']);

Route::get('/sacar_empleados', [EmpleadoController::class, 'sacarEmpleados']);