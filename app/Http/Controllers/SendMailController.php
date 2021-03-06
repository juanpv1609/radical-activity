<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use App\Models\Contrato;
use App\Mail\TareasEmail;
use App\Mail\ContratoEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    public function index()
    {
        //
    }
    public function sendMailsClient($contrato_id)
    {
        $contrato = Contrato::with('cliente', 'pais', 'area', 'tarea')->find($contrato_id);
        $correos = explode(",", $contrato->correos);
        $details = [

        'title' => 'Nuevo contrato registrato',
        'body' => 'Se ha registrado un nuevo contrato, a continuacion los detalles',
        'cliente' => $contrato->cliente->razon_social,
        'fecha_inicio' => $contrato->fecha_inicio,
        'fecha_fin' => $contrato->fecha_fin,
        'area' => $contrato->area->nombre,
        'solucion' => $contrato->solucion,

        ];
        foreach ($correos as $correo) {
            Mail::to($correo)->send(new ContratoEmail($details));

        }
    }
    public function sendMailUser($tarea_id)
    {
        
        $tarea = Tareas::with('contrato', 'frecuencias','estado_tarea','tipo','usuario')
                            ->find($tarea_id);
                            //->whereRaw('fecha_alerta = curdate()')->get();
                            //dd($tarea);
                    $details = [

                        'title' => 'Notificacion de alerta',
                        'body' => 'Estimad@ '.$tarea->usuario->name.' el software RGSDM (Radical Gestión SDM) ha generado la siguiente alerta:',
                        'entregable' => $tarea->descripcion,
                        'cliente' => $tarea->contrato->cliente->razon_social,
                        'descripcion_contrato' => $tarea->contrato->descripcion,
                        'observacion_contrato' => $tarea->contrato->observacion,
                        'fecha_entrega' => $tarea->fecha,
                        'fecha_alerta' => $tarea->fecha_alerta,
                        'tipo_tarea' => $tarea->tipo->nombre.' '.$tarea->frecuencias->descripcion,

                    ];
                    Mail::to($tarea->usuario->email)
                    ->cc(['paul.canchignia@gruporadical.com','xavier.montoya@gruporadical.com'])
                    ->send(new TareasEmail($details));
                    $tarea->alerta_enviada = 1;
                    $tarea->save();

        //$this->info('Successfully sent daily quote to everyone.');
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
