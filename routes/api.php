<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\HorariosController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ActividadesController;
use App\Http\Controllers\NivelEstudioController;
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
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::post('login', [LoginController::class,'login']);
    Route::post('logout', [LoginController::class,'logout']);



    Route::resource('paises', PaisController::class);
    Route::resource('horarios', HorariosController::class);
    Route::resource('actividades', ActividadesController::class);
    Route::resource('tipo-actividad', TipoActividadController::class);
    Route::resource('usuarios', UserController::class);
    Route::get('usuarios-all', [UserController::class,'indexAll']);
    Route::post('usuario-updatePassword', [UserController::class,'usuarioUpdatePassword']);


    Route::get('tipo-identificacion', [ConfigController::class,'identificacion']);
    Route::get('estado-tareas', [ConfigController::class,'estadoTareas']);
    Route::get('tipo-tareas', [ConfigController::class,'tipoTareas']);
    Route::get('estado-estudio', [ConfigController::class,'estadoEstudio']);

    Route::resource('persona', PersonaController::class);
    Route::get('persona-estudios/{id}', [PersonaController::class,'estudios']);
    Route::resource('nivel-estudio', NivelEstudioController::class);
    Route::get('get-nivel-estudio', [NivelEstudioController::class,'listarNiveles']);
    Route::resource('certificaciones', CertificacionesController::class);
    Route::resource('certificaciones', CertificacionesController::class);
    Route::get('get-certificaciones', [CertificacionesController::class,'listarCertificaciones']);


// send mails
    Route::get('send-mails-client/{contrato}', [SendMailController::class, 'sendMailsClient']);
    Route::get('send-mail-user/{tarea}', [SendMailController::class, 'sendMailUser']);

    Route::get('reporte-certificaciones/{certificaciones}', [ReportesController::class, 'reporteCertificaciones']);
    Route::get('reporte-persona/{persona}', [ReportesController::class, 'reportePersona']);


});

