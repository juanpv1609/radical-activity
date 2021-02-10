<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tarea;
use App\Models\Tareas;
use App\Models\Contrato;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index()
    {
        //$products = Product::all()->toArray();
        $cond=[
            'responsable' => auth()->user()->id
        ];
        $tareas = Tareas::with('contrato.cliente', 'frecuencias','estado_tarea','tipo','usuario')->where($cond)->get()->toArray();

        return $tareas;
    }

    public function store(Request $request)
    {

        $data = $request->input("tareas");
        foreach ($data as $item) {
            $fecha = Carbon::createFromFormat('Y-m-d',$item['fecha']);
            $fecha_alerta = $fecha->subDays($item['alerta']);
            $tarea = new Tareas([
                    'contrato_id' => $item['contrato_id'],
                    'frecuencia' => $item['frecuencia'],
                    'tipo_tarea' => $item['tipo_tarea'],
                    'dia_elegido' => $item['dia_elegido'],
                    //'ticket' => $item['id'],
                    'responsable' => $item['responsable'],
                    'fecha' => $item['fecha'],
                    'alerta' => $item['alerta'],
                    'fecha_alerta' => $fecha_alerta,
                    'estado' => 1,

                    ]);
                    $tarea->save();

        }




        return response()->json('Task created!');
    }

    public function show($id)
    {
        $contrato = Tareas::with('contrato.cliente', 'frecuencias','estado_tarea','tipo')->find($id);
        return response()->json($contrato);
    }
    public function contratoTareas($id)
    {
        $contrato = Tareas::with('contrato.cliente', 'frecuencias','estado_tarea','tipo')->where('contrato_id','=',$id)->get();
        return response()->json($contrato);
    }
    public function update($id, Request $request)
    {
        $arrayAdjuntos = $request->input("adjuntos");

        $tarea = Tareas::find($id);
        $tarea->ticket = $request->input('ticket');
        $tarea->estado = $request->input('estado');
        $tarea->observacion = $request->input('observacion');
        $tarea->adjunto = implode(",", $arrayAdjuntos);


        $tarea->save();

        return response()->json('Tarea updated!');
    }
    public function updateGroup(Request $request)
    {

        $data = $request->input("tareas");

        foreach ($data as $item) {
            //$tarea = Tareas::where('id', '=', $aux->id)->first();
                $tarea = Tareas::find($item['id']);

                $tarea->responsable = $item['responsable'];
                $tarea->tipo_tarea = $item['tipo_tarea'];
                $tarea->fecha = $item['fecha'];
                $tarea->save();

        }



        return response()->json('Tarea updated!');
    }
    public function subirArchivo(Request $request)
    {
        $file = $request->file('file');

        $fileName = time().'-'.$request->file->getClientOriginalName();
        //$request->file->move(public_path('upload'), $fileName);
        \Storage::disk('local')->put($fileName, \File::get($file));


        return response()->json(['archivo'=>$fileName]);
    }

    public function destroy($id)
    {
        $contrato = Tareas::find($id);
        $contrato->delete();

        return response()->json('Product deleted!');
    }
}