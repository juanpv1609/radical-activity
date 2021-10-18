<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;

class HorariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $cond=['estado' => 1];

        $horarios = Horario::where($cond)->get()->toArray();

        return $horarios;

    }

    public function store(Request $request)
    {
        $horario = new Horario([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'inicio' => $request->input('inicio'),
            'fin' => $request->input('fin'),
            'estado' => 1,
        ]);
        $horario->save();

        return response()->json('horario created!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $horario = Horario::find($id);
        return response()->json($horario);

    }


    public function update(Request $request, $id)
    {
         $horario = Horario::find($id);
        $horario->update($request->all());

        return response()->json('horario updated!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $horario = Horario::find($id);
        $horario->estado=0;
        $horario->save();
        //$area->delete();

        return response()->json('horario deleted!');

    }
}
