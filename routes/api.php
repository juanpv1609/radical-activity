<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PanetController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\HorariosController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ActividadesController;
use App\Http\Controllers\NivelEstudioController;
use App\Http\Controllers\PerfilPuestoController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\TipoActividadController;
use App\Http\Controllers\CertificacionesController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/





// Route::middleware('api')->get('user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('api')->group(function () {
    /* Route::get('user', function (Request $request) {
        return $request->user();
    }); */
    Route::get('user', [UserController::class,'getUser']);
    Route::get('user-activity/{id}', [UserController::class,'getUserActivity']);


    Route::post('login', [LoginController::class,'login']);
    Route::post('logout', [LoginController::class,'logout']);


    Route::resource('areas', AreaController::class);
    Route::resource('clasificacion', ClasificacionController::class);


    Route::resource('paises', PaisController::class);
    Route::resource('horarios', HorariosController::class);
    Route::resource('actividades', ActividadesController::class);
    Route::get('mi-actividad/{usuario}', [ActividadesController::class, 'miActividad']);

    Route::get('detalle-actividades/{id}', [ActividadesController::class,'detalleActividades']);
    Route::get('verificar-actividades/{fecha}/{usuario}', [ActividadesController::class,'verificarActividades']);

    Route::get('actividades-calendar', [ActividadesController::class,'actividadesCalendar']);
    Route::get('mi-actividad-calendar/{usuario}', [ActividadesController::class,'miActividadCalendar']);



    Route::resource('tipo-actividad', TipoActividadController::class);
    Route::resource('usuarios', UserController::class);
    Route::get('usuarios-all', [UserController::class,'indexAll']);
    Route::get('usuarios-area/{area}', [UserController::class,'indexByArea']);

    Route::post('usuario-updatePassword', [UserController::class,'usuarioUpdatePassword']);
    Route::resource('perfil-puesto', PerfilPuestoController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('dashboard', DashboardController::class);
    Route::get('dashboardPorTipo', [DashboardController::class,'porTipo']);

    Route::get('dashboardPorCliente', [DashboardController::class,'porCliente']);

    Route::get('dashboardPorUsuario', [DashboardController::class,'porUsuario']);


    Route::get('dashboardPorFecha', [DashboardController::class,'porFecha']);

    Route::get('dashboardCalendario/{usuario}', [DashboardController::class,'calendario']);






// send mails
    Route::get('send-mails-client/{contrato}', [SendMailController::class, 'sendMailsClient']);
    Route::get('send-mail-user/{tarea}', [SendMailController::class, 'sendMailUser']);

    Route::get('reporte-certificaciones/{certificaciones}', [ReportesController::class, 'reporteCertificaciones']);
    Route::get('reporte-persona/{persona}', [ReportesController::class, 'reportePersona']);
    Route::get('reporte-actividades/{inicio}/{fin}/{usuarios}', [ReportesController::class, 'reporteActividades']);
    Route::get('reporte-actividadesXLSX/{inicio}/{fin}/{usuarios}', [ReportesController::class, 'reporteActividadesXLSX']);

    Route::get('reporte-actividades-contable/{fechas}/{usuarios}', [ReportesController::class, 'reporteActividadesContable']);

// PROACTIVANET
    Route::post('close-tickets', [PanetController::class,'closeTicket']);
    Route::post('verify-tickets', [PanetController::class,'verifyTicket']);

    Route::get('customers', [PanetController::class,'getCustomers']);
    Route::get('types', [PanetController::class,'getTypes']);





});

