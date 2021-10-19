<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;
use App\Models\TipoActividad;

class TipoActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cond=['is_deleted' =>0];

        $tipoActividad = TipoActividad::where($cond)->orderBy('descripcion')->get()->toArray();

        return ($tipoActividad);
    }

    public function store(Request $request)
    {
        $tipoActividad = new TipoActividad([
            'descripcion' => $request->input('descripcion'),
            'estado' => $request->input('estado'),
        ]);
        $tipoActividad->save();

        return response()->json('tipoActividad created!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $tipoActividad = TipoActividad::find($id);
        return response()->json($tipoActividad);

    }


    public function update(Request $request, $id)
    {
        $tipoActividad = TipoActividad::find($id);
        $tipoActividad->update($request->all());

        return response()->json('tipoActividad updated!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipoActividad = TipoActividad::find($id);
        $tipoActividad->is_deleted=1;
        $tipoActividad->save();
        //$area->delete();

        return response()->json('tipoActividad deleted!');

    }
}
