<?php

namespace App\Http\Controllers;

use App\Models\Clasificacion;
use Illuminate\Http\Request;

class ClasificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cond=['is_deleted' => 0];

        $clasificacion = Clasificacion::where($cond)->orderBy('nombre','asc')->get()->toArray();

        return ($clasificacion);

    }
    public function store(Request $request)
    {
        //dd($request);
        $clasificacion = new Clasificacion([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'color' => ($request->input('color')!==null) ? $request->input('color') : 'primary',
            'estado' => $request->input('estado'),
            'is_deleted' => 0,
        ]);
        $clasificacion->save();

        return response()->json('clasificacion created!');
    }


    public function show($id)
    {
        $clasificacion = Clasificacion::find($id);
        return response()->json($clasificacion);
    }

    public function update($id, Request $request)
    {
        $clasificacion = Clasificacion::find($id);
        $clasificacion->nombre  = $request->input('nombre');
        $clasificacion->descripcion = $request->input('descripcion');
        $clasificacion->color   = $request->input('color');
        $clasificacion->estado  = $request->input('estado');

        $clasificacion->save();


        return response()->json('clasificacion updated!');
    }

    public function destroy($id)
    {
        $clasificacion = Clasificacion::find($id);

        $clasificacion->is_deleted=1;
        $clasificacion->save();


        return response()->json('clasificacion deleted!');
    }
}

