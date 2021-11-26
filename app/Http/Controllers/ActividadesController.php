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
        //$actividades = Actividades::with('actividad.usuario_id', 'actividad.horario', 'tipo', 'status')->get()->toArray();

        //return ($actividades);
        $usuario_estandar= User::where('role',1)->get()->toArray();
        $arrayUsuarios = [];
         foreach ($usuario_estandar as $usuario) {
             //$actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->where('dia', $item['id'])->get()->toArray();
             array_push($arrayUsuarios, $usuario['id']);
         }
         array_push($arrayUsuarios, auth()->user()->id);

         $actividad = Actividad::with('usuario','horario')->whereIn('usuario_id', $arrayUsuarios)->get();
         foreach ($actividad as $item) {
             # code...
             $actividades = Actividades::where('dia',$item->id)->get();
             $item->actividades=count($actividades);

         }



        return ($actividad);

    }
    public function indexcopy()
    {
        //$actividades = Actividades::with('actividad.usuario_id', 'actividad.horario', 'tipo', 'status')->get()->toArray();

        //return ($actividades);
        $usuario_estandar= User::where('role',1)->get()->toArray();
        $arrayUsuarios = [];
         foreach ($usuario_estandar as $usuario) {
             //$actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->where('dia', $item['id'])->get()->toArray();
             array_push($arrayUsuarios, $usuario['id']);
         }

         $actividad = Actividad::whereIn('usuario_id', $arrayUsuarios)->get()->toArray();

         $arrayActividades = [];
         //dd($actividad);
         foreach ($actividad as $item) {
             //$actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->where('dia', $item['id'])->get()->toArray();
             array_push($arrayActividades, $item['id']);
            }
            $actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->whereIn('dia', $arrayActividades)->get()->toArray();


        return ($actividades);

    }

    public function store(Request $request)
    {
        $arrayActivities = $request->input("activities");
        //dd($arrayActivities);
        $actividad = new Actividad([
            'usuario_id'        => $request->input('usuario'),
            'horario_id'        => $request->input('horario'),
            'fecha'          => $request->input('fecha'),
            'destinatarios'  => (count($request->input('destinatarios'))>0) ? implode(",", $request->input('destinatarios')) : null,
        ]);
        $actividad->save();
        $horas_p=0.0;
        $horas_np=0.0;
        $array_horas_inicio=[];
        $array_horas_fin=[];


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
                'estado' => ($item['estado']==true) ? 1 : 2,
                'verificada' => 0,
                //'is_verified_by' => $item['is_verified_by'],


            ];
            array_push($array_horas_inicio,$item['h_inicio']);
            array_push($array_horas_fin, $item['h_fin']);

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
        $actividad = Actividad::where('usuario_id',$id)->get();
         foreach ($actividad as $item) {
            # code...
            $actividades = Actividades::where('dia', $item->id)->get();
            $item->actividades=count($actividades);
        }



        return ($actividad);
    }
    public function detalleActividades($id)
    {

            $actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->where('dia', $id)->get()->toArray();


        return ($actividades);
    }


    public function update(Request $request, $id)
    {
        $arrayActivities = $request->input("activities");

        $actividad = Actividad::find($id);
        $actividad->usuario_id = $request->input('usuario');
        $actividad->horario_id = $request->input('horario');
        $actividad->fecha = $request->input('fecha');
        $actividad->destinatarios = (count($request->input('destinatarios'))>0) ? implode(",", $request->input('destinatarios')) : null;
        $actividad->save();


        $actividades = Actividades::where('dia', $actividad->id)->get();
        //dd($arrayActivities);
        foreach ($actividades as $item) {
            $item->delete();
        }


         foreach ($arrayActivities as $item) {

            $actividades = new Actividades([
                'tipo_actividad' => $item['tipo_actividad'],
                'descripcion' => implode(",", $item['descripcion']),
                'colaboradores' => isset($item['colaboradores']) ? implode(",", $item['colaboradores']) : null,
                'dia' => $actividad->id,
                'h_inicio' => $item['h_inicio'],
                'h_fin' => $item['h_fin'],
                'observacion' => $item['observacion'],
                'estado' => ($item['estado']==true) ? 1 : 2,
                'verificada' => 0,
                //'is_verified_by' => $item['is_verified_by'],


            ]);

            $actividades->save();
        }


        return response()->json('activities updated!');

    }


    public function destroy($id)
    {
        $actividad = Actividad::find($id);

        $actividades = Actividades::where('dia', $actividad->id)->get();
        foreach ($actividades as $act) {
            $act->delete();
        }
        $actividad->delete();

    }
}
