<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use App\Models\PersonaEstudio;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arr = [
            'estado' => 1,
            'is_deleted' => 0,
        ];
        $personas = Persona::with('pais','nacionalidad')->where($arr)->get();
        return $personas;
    }

    public function store(Request $request)
    {
        $arrayEstudios = $request->input("estudios");
        $persona = new Persona([
            'ci'                => $request->input('ci'),
            'nombre'            => $request->input('nombre'),
            'apellido'          => $request->input('apellido'),
            'email'             => $request->input('email'),
            'pais_id'           => $request->input('pais_id'),
            'nacionalidad_id'   => $request->input('nacionalidad_id'),
            'telefono'          => $request->input('telefono'),
            'fecha_nacimiento'  => $request->input('fecha_nacimiento'),
            'foto'              => $request->input('fotoString')
        ]);
        $persona->save();
        foreach ($arrayEstudios as $item) {
            $estudios = new PersonaEstudio([
                'persona_id' => $persona->id,
                'pais_id' => $item['pais_id'],
                'titulo' => $item['titulo'],
                'descripcion' => $item['descripcion'],
                'nivel_id' => $item['nivel_id'],
                'certificado' => $item['certificado'],
                'estado_estudio_id' => $item['estado_estudio_id'],
                'institucion' => $item['institucion'],
                'fecha_inicio' => $item['fecha_inicio'],
                'fecha_fin' => $item['fecha_fin'],
                'duracion_horas' => $item['duracion_horas'],
                'documentos' => isset($item['documentos']) ? implode(",",$item['documentos']) : null,

            ]);
            $estudios->save();
        }
        return response()->json('Person created!');
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

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
