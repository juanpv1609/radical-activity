<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Actividad;
use App\Models\Actividades;
use Illuminate\Http\Request;
use App\Mail\ActividadesEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ActividadesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->get()->toArray();

        return ($actividades);
    }

    public function store(Request $request)
    {
        $arrayActivities = $request->input("activities");
        $actividad = new Actividad([
            'usuario'        => $request->input('usuario'),
            'horario'        => $request->input('horario'),
            'fecha'          => $request->input('fecha'),
            'destinatarios'  => implode(",", $request->input('destinatarios')),
        ]);
        $actividad->save();
        $listaActividades = [];
        foreach ($arrayActivities as $item) {
            $aux = [
                'tipo_actividad' => $item['tipo_actividad'],
                'descripcion' => implode(",", $item['descripcion']),
                'colaboradores' => implode(",", $item['colaboradores']),
                'dia' => $actividad->id,
                'h_inicio' => $item['h_inicio'],
                'h_fin' => $item['h_fin'],
                'observacion' => $item['observacion'],
                'estado' => $item['estado'] ? 1 : 0,

            ];
            $actividades = new Actividades($aux);
            $actividades->save();
            array_push($listaActividades, $aux);
        }
        if ($request->input('enviaMail')) {
            $usuario = User::where('id', $request->input('usuario'))->first();
            $details = [
                'usuario' => $usuario->email,
                'title' => 'Control de Actividades Diarias',
                'body' => 'El usuario: ' . $usuario->name . 'ha registrado sus actividades diarias correspondientes al: ' . $request->input('fecha'),
                'actividades' => $listaActividades,

            ];
            //dd($details);
            $arrayDestinatarios = [];
            foreach ($request->input('destinatarios') as $dest) {
                array_push($arrayDestinatarios, $dest . '@gruporadical.com');
            }
            Mail::to($arrayDestinatarios)
                ->send(new ActividadesEmail($details));
        }
        return response()->json('activities created!');
    }

    public function show($id)
    {
        $actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->where('actividad.usuario', $id)->get()->toArray();

        return ($actividades);
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
