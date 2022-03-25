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
        $role=[];
        $cargo=[];
        if (auth()->user()->role==2) { //ADMINISTRADOR
            $role = [1,2,3];
            $cargo = [1,2,3,4,5,6,7,8,9,10,11];


        } else if(auth()->user()->role==3) { //SUPERVISOR
            $role = [1,3];
            $cargo = [1,2,3,4,5,6,7,8,9,10,11];

            if ((auth()->user()->cargo==9) || (auth()->user()->cargo==10)) { // Jefe de operaciones
                $role = [1,3];
                $cargo = [1,2,3,4,5,6,7,8,9,10,11];
            } else if (auth()->user()->cargo==4) { // coordinador del CERT
                $role = [1,3];
                $cargo = [1,3,4];
            } else if (auth()->user()->cargo==8) { //coordinador del infraestructura
                $role = [1];
                $cargo = [7];
            } else if (auth()->user()->cargo==6) { //coordinador del infraestructura
                $role = [1];
                $cargo = [5];
            }
        }


        $usuario_estandar = User::with('rol','puesto.area')->where('is_deleted',0)->whereIn('role',$role)->whereIn('cargo',$cargo)->get();
        $arrayUsuarios = [];
         foreach ($usuario_estandar as $usuario) {
             //$actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->where('dia', $item['id'])->get()->toArray();


             array_push($arrayUsuarios, $usuario->id);
         }
         array_push($arrayUsuarios, auth()->user()->id);

         $actividad = Actividad::with('usuario.puesto.area','horario')->whereIn('usuario_id', $arrayUsuarios)->orderBy('fecha','desc')->get();
         foreach ($actividad as $item) {
             # code...
             $actividades = Actividades::where('dia', $item->id)->get();
            $item->actividades_count=$actividades->count();
            //$item->actividades=$actividades;

             $item->hora_inicio = $actividades->min('h_inicio');
             $item->hora_fin= $actividades->max('h_fin');


         }



        return ($actividad);

    }
    public function actividadesCalendar()
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

         $actividad = Actividad::with('usuario.puesto.area','horario')->whereIn('usuario_id', $arrayUsuarios)->orderBy('fecha','desc')->get();
         foreach ($actividad as $item) {
             # code...
             $actividades = Actividades::with('tipo')->where('dia', $item->id)->get();
            $item->actividades_count=$actividades->count();
            $item->actividades=$actividades;

             $item->hora_inicio = $actividades->min('h_inicio');
             $item->hora_fin= $actividades->max('h_fin');


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
            'horas_p'           => $request->input('horas_p'),
            'horas_np'          => $request->input('horas_np'),
            'horas_total'       => $request->input('horas_total'),

            'fecha'          => $request->input('fecha'),
            'destinatarios'  => (count($request->input('destinatarios'))>0) ? implode(",", $request->input('destinatarios')) : null,
            'es_planificada' => $request->input('planificada'),

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
                'estado' => ($item['estado']==true) ? 1 : 2,
                'verificada' => 0,
                //'is_verified_by' => $item['is_verified_by'],


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
        $actividad = Actividad::with('usuario.puesto.area','horario')->where('usuario_id',$id)->orderBy('fecha','desc')->get();
        foreach ($actividad as $item) {
            # code...
            $actividades = Actividades::with('tipo')->where('dia', $item->id)->get();
            $item->actividades_count=$actividades->count();
            $item->actividades=$actividades;

            $item->hora_inicio = $actividades->min('h_inicio');
            $item->hora_fin= $actividades->max('h_fin');
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
        //dd($request);
        $actividad = Actividad::find($id);
        $actividad->usuario_id = $request->input('usuario');
        $actividad->horario_id = $request->input('horario');
        $actividad->horas_p = $request->input('horas_p');
        $actividad->horas_np = $request->input('horas_np');
        $actividad->horas_total = $request->input('horas_total');

        $actividad->fecha = $request->input('fecha');
        $actividad->es_planificada = $request->input('planificada');

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
