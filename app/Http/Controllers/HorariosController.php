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
        if (auth()->user()->role==1) { //estandar
            if((auth()->user()->cargo==3)||(auth()->user()->cargo==5)||(auth()->user()->cargo==7)){ //N2
                $cond=['estado' => 1,
                        'perfil_puesto' => 3];

            }else {
                $cond=['estado' => 1,
                        'perfil_puesto' => auth()->user()->cargo];

            }


        }else if (auth()->user()->role==2){ //admin
            $cond=['estado' => 1];

        }else if (auth()->user()->role==3){ //supervisor
            $cond=['estado' => 1,
                'perfil_puesto' => 3];

        }


        $horarios = Horario::with('cargo')->where($cond)->orderBy('nombre')->get()->toArray();

        return $horarios;

    }

    public function store(Request $request)
    {
        $horario = new Horario([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'inicio' => $request->input('inicio'),
            'fin' => $request->input('fin'),
            'perfil_puesto' => $request->input('perfil_puesto'),
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
