<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){
        $arrayUsuarios=[];

        if (auth()->user()->role>=2) {
            $usuario_estandar= User::get()->toArray();
            foreach ($usuario_estandar as $usuario) {
                array_push($arrayUsuarios, $usuario['id']);
            }

        }else{
            array_push($arrayUsuarios, auth()->user()->id);

        }


         //$actividad = Actividad::with('usuario', 'horario')->whereIn('usuario_id', $arrayUsuarios)->orderBy('fecha', 'desc')->get();

         $result = DB::table('actividad')
                ->join('users', 'users.id', '=', 'actividad.usuario_id')
                ->select('actividad.usuario_id','users.name', DB::raw('SUM(horas_total) as total'))
                ->whereIn('actividad.usuario_id',$arrayUsuarios)
                ->groupBy('actividad.usuario_id','users.name')
                ->orderBy('total','DESC')
                ->get();


        return ($result);

    }

    public function calendario($usuario){
        $result = DB::table('actividad')
            ->select('fecha', DB::raw('SUM(horas_total) as total'))
            ->where('usuario_id', auth()->user()->id)
            ->groupBy('fecha')
            ->get();
        return $result;

    }
    public function porTipo(){
        $arrayUsuarios=[];

        if (auth()->user()->role>=2) {
            $usuario_estandar= User::get()->toArray();
            foreach ($usuario_estandar as $usuario) {
                array_push($arrayUsuarios, $usuario['id']);
            }
        } else {
            array_push($arrayUsuarios, auth()->user()->id);
        }

        $result = DB::table('actividades')
            ->join('actividad', 'actividad.id', '=', 'actividades.dia')
            ->join('tipo_actividad', 'tipo_actividad.id', '=', 'actividades.tipo_actividad')
            ->select('tipo_actividad.descripcion', DB::raw('SUM(actividad.horas_total) as total'))
            //->where('actividades.h_inicio','=','22:00:00')
            ->whereIn('actividad.usuario_id',$arrayUsuarios)
            ->groupBy('tipo_actividad.descripcion')
            ->get();
        return $result;
    }
    public function porFecha(){
        $arrayUsuarios=[];

        if (auth()->user()->role>=2) {
            $usuario_estandar= User::get()->toArray();
            foreach ($usuario_estandar as $usuario) {
                array_push($arrayUsuarios, $usuario['id']);
            }
        } else {
            array_push($arrayUsuarios, auth()->user()->id);
        }

        $result = DB::table('actividad')
            ->select('fecha', DB::raw('SUM(horas_total) as total'))
            ->where('usuario_id',auth()->user()->id)
            ->groupBy('fecha')
            ->havingRaw('SUM(horas_total) > ?', [0])
            ->orderBy('fecha','ASC')
            ->get();
        return $result;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
