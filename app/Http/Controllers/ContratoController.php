<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SendMailController;

class ContratoController extends Controller
{
    public function index()
    {
        //$products = Product::all()->toArray();
        $contratos = Contrato::with('cliente', 'pais', 'area', 'tarea')->get()->toArray();

        return $contratos;
    }

    public function store(Request $request)
    {


        $arrayCorreos = $request->input("correos");
        $arrayAdjuntos = $request->input("adjuntos");
        $contrato = new Contrato([
            'descripcion' => $request->input("descripcion"),
            'fecha_inicio' => $request->input("fecha_inicio"),
            'fecha_fin' => $request->input("fecha_fin"),
            'cliente_id' => $request->input('cliente'),
            'pais_id' => $request->input('pais'),
            'area_id' => $request->input('area'),
            'solucion' => $request->input('solucion'),
            'marca' => $request->input('marca'),
            'correos' => implode(",", $arrayCorreos),
            'adjunto' => implode(",", $arrayAdjuntos),
            'observacion' => $request->input("observacion"),
            'estado' => 1,
        ]);
        $contrato->save();
        // $contratoObject = new SendMailController();
        // $contratoObject->sendMailsClient($contrato->id);

        return response()->json('Contrato created!');
    }

    public function show($id)
    {
        $contrato = Contrato::with('cliente', 'pais', 'area', 'tarea')->find($id);
        return response()->json($contrato);
    }

    public function update($id, Request $request)
    {
        $arrayCorreos = $request->input("correos");
        $arrayAdjuntos = $request->input("adjuntos");

        $contrato = Contrato::find($id);
        // BORRAR ARCHIVOS ANTERIORES
        if ($contrato->adjunto!=='') {
            $arrayAdjuntosOld = explode(",", $contrato->adjunto);
            foreach ($arrayAdjuntosOld as $item) {
                if (Storage::disk('local')->exists($item)) {
                    Storage::delete($item);
                }
            }
        }

        $contrato->descripcion = $request->input("descripcion");
        $contrato->fecha_inicio = $request->input("fecha_inicio");
        $contrato->fecha_fin = $request->input("fecha_fin");
        $contrato->cliente_id = $request->input("cliente");
        $contrato->pais_id = $request->input("pais");
        $contrato->area_id = $request->input("area");
        $contrato->solucion = $request->input("solucion");
        $contrato->marca = $request->input("marca");
        $contrato->correos = implode(",", $arrayCorreos);
        $contrato->adjunto = implode(",", $arrayAdjuntos);
        $contrato->observacion = $request->input("observacion");
        $contrato->estado = 1;
        $contrato->save();

        return response()->json('Product updated!');
    }

    public function destroy($id)
    {
        $contrato = Contrato::find($id);
        $contrato->delete();

        return response()->json('Product deleted!');
    }
}
