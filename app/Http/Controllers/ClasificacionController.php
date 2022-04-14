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
        $clasificacion = new Clasificacion([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'estado' => 1
        ]);
        $clasificacion->save();

        return response()->json('pais created!');
    }


    public function show($id)
    {
        $clasificacion = Clasificacion::find($id);
        return response()->json($clasificacion);
    }

    public function update($id, Request $request)
    {
        $clasificacion = Clasificacion::find($id);
        $clasificacion->update($request->all());

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

