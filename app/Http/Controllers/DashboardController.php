<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actividades = DB::table('actividades')
            ->join('actividad', 'actividad.id', '=', 'actividades.dia')
            ->join('users', 'users.id', '=', 'actividad.usuario_id')
            ->join('tipo_actividad', 'tipo_actividad.id', '=', 'actividades.tipo_actividad')
            ->select('actividad.fecha', 'tipo_actividad.descripcion', 'users.name','actividades.h_inicio','actividades.h_fin')
            ->where('users.id','<>',19)
            //->where('actividades.h_inicio','=','22:00:00')
            ->orderBy('users.name','asc')->get();

        $result = array();
        foreach ($actividades as $actividad ) {
            $inicio= new Carbon($actividad->h_inicio);
            $fin= new Carbon($actividad->h_fin);
            $repeat=false;

            if ($actividad->h_inicio=='22:00:00') { //verificar si funciona
                $actividad->total=8.0;
                //$actividad->tipo=3;
            }
            else{

                $actividad->total= ($inicio->diffInMinutes($fin))/60; //restar tiempos
            }

             for ($i=0;$i<count($result);$i++) {
                if ($result[$i]['name']==$actividad->name) {
                    $result[$i]['total']+=$actividad->total;

                    $result[$i]['total']=round($result[$i]['total'], 2);
                    $repeat=true;
                    break;
                }
            }

            if ($repeat==false) {
                $result[] = array('name' => $actividad->name, 'total' => $actividad->total);
            }

        }
        return $result;
    }
    public function calendario($usuario){
        $actividades = Actividad::where('usuario_id',$usuario)->get();
        return $actividades;
    }
    public function porTipo()
    {
        $actividades = DB::table('actividades')
            ->join('actividad', 'actividad.id', '=', 'actividades.dia')
            ->join('users', 'users.id', '=', 'actividad.usuario_id')
            ->join('tipo_actividad', 'tipo_actividad.id', '=', 'actividades.tipo_actividad')
            ->select('actividad.fecha', 'tipo_actividad.descripcion', 'users.name','actividades.h_inicio','actividades.h_fin')
            ->where('users.id','<>',19)
            //->where('actividades.h_inicio','=','22:00:00')
            ->orderBy('users.name','asc')->get();

        $result = array();
        foreach ($actividades as $actividad ) {
            $inicio= new Carbon($actividad->h_inicio);
            $fin= new Carbon($actividad->h_fin);
            $repeat=false;

            if ($actividad->h_inicio=='22:00:00') { //verificar si funciona
                $actividad->total=8.0;
                //$actividad->tipo=3;
            }
            else{

                $actividad->total= ($inicio->diffInMinutes($fin))/60; //restar tiempos
            }

             for ($i=0;$i<count($result);$i++) {
                if ($result[$i]['descripcion']==$actividad->descripcion) {
                    $result[$i]['total']+=$actividad->total;

                    $result[$i]['total']=round($result[$i]['total'], 2);
                    $repeat=true;
                    break;
                }
            }

            if ($repeat==false) {
                $result[] = array('descripcion' => $actividad->descripcion, 'total' => $actividad->total);
            }

        }
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
