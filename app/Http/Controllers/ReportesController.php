<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\User;
use App\Models\Persona;
use App\Models\Actividad;
use App\Models\Actividades;
use Illuminate\Http\Request;
use App\Models\PersonaEstudio;
use App\Models\Certificaciones;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;

class ReportesController extends Controller
{


     public function reporteCertificaciones($certificaciones)
    {
        $arrayCertificaciones = explode(",",$certificaciones);
        $data = Certificaciones::whereIn('id',$arrayCertificaciones)->get();
        //$data = PersonaEstudio::with('pais','persona','nivelEstudio','estadoEstudio','certificaciones')->whereIn('certificado',$certificaciones)->get();

        $dataCertificaciones = [];
        $total_personas=0;
        foreach ($data as $item) {
            $personaEstudio = PersonaEstudio::with('pais','persona','nivelEstudio','estadoEstudio','certificaciones')
            ->where('certificado', $item->id)->get();
            //$dataCertificaciones['certificacion'] = $data[$i]->solucion;
            //$auxPerson['certificacion'] = $data[$i]->solucion;
            $auxCertificaciones = [];
            $personas = [];
            foreach ($personaEstudio as $person) {
                $auxPerson = array(
                    "nombre" => $person->persona->nombre.' '.$person->persona->apellido,
                    "pais" => $person->pais->nombre,
                    "pais_abreviatura" => $person->pais->abreviatura,
                    "estado" => $person->estadoEstudio->descripcion,
                    "estado_color" => $person->estadoEstudio->color,
                );
                //$auxPerson['nombre'] = $person->persona->nombre.' '.$person->persona->apellido;
                array_push($personas, $auxPerson);
            }
            $total_personas += count($personaEstudio);
            $auxCertificaciones = array(
                'certificacion' => $item->solucion,
                'total' => count($personaEstudio),
                'total_personas' => $total_personas,
                'personas' => $personas
            );
            array_push($dataCertificaciones,$auxCertificaciones);
        }

        $pdf = App::make('dompdf.wrapper');

        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->loadView('pdf.certificaciones', compact('dataCertificaciones'));
        return $pdf->download('reporteCertificaciones.pdf');
    }
    public function reporteActividades($inicio,$fin,$users)
    {
        //dd($usuarios);
        $inicio = $inicio;
        $fin = $fin;
        $usuarios = explode(",", $users);
        //dd($request);
        $personas=[];
        $aux=[];

        $persona=[];
        $personas['inicio'] = $inicio;
        $personas['fin'] = $fin;

        foreach ($usuarios as $user) {
            $usuario = User::find($user);
            $actividad = Actividad::where('usuario_id', $usuario->id)->whereBetween('fecha',[$inicio,$fin])->get()->toArray();
            $arrayActividades = [];
            $persona['usuario'] = $usuario->name;
            foreach ($actividad as $item) {
                //$actividades = Actividades::with('actividad.usuario', 'actividad.horario', 'tipo', 'status')->where('dia', $item['id'])->get()->toArray();
                array_push($arrayActividades, $item['id']);
            }
                $actividades = Actividades::with( 'tipo', 'status','actividad')->whereIn('dia', $arrayActividades)->get()->toArray();
                $persona['actividades'] = $actividades;
                array_push($aux,$persona);
        }
        $personas['usuarios'] = $aux;

        //dd($personas);
        $pdf = App::make('dompdf.wrapper');

        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->loadView('pdf.actividades', compact('personas'));
        return $pdf->download('reporteActividades_'.$inicio.'_'.$fin.'.pdf');
    }
    public function reportePersona($id)
    {
            $persona = Persona::with('pais','nacionalidad')->findOrFail($id);
            $personaEstudio = PersonaEstudio::with('pais','persona','nivelEstudio','estadoEstudio','certificaciones')
            ->where('persona_id', $persona->id)->get();
            $estudios = [];
            $certificaciones = [];
            $auxPersona = [];
            $dataPersona = [];
            foreach ($personaEstudio as $item) {
                $auxEstudios = array(
                    "descripcion" => $item->descripcion,
                    "institucion" => $item->institucion,
                    "titulo" => $item->titulo,
                    "nivel" => $item->nivelEstudio->descripcion,
                    "inicio" => $item->fecha_inicio,
                    "fin" => $item->fecha_fin,
                    "certificado" => $item->certificado,
                    "estado" => $item->estadoEstudio->descripcion,
                    "estado_color" => $item->estadoEstudio->color,
                );
                if ($item->certificado!==null) {
                    # code...
                    $auxCertificaciones = array(
                        "solucion" => $item->certificaciones->solucion,
                        "descripcion" => $item->descripcion,
                        "institucion" => $item->institucion,
                        "link" => $item->documentos,
                    );
                    array_push($certificaciones, $auxCertificaciones);
                }
                array_push($estudios, $auxEstudios);

            }
            //dd($certificaciones);

        $pdf = App::make('dompdf.wrapper');

        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->loadView('pdf.persona', compact(['persona','estudios','certificaciones']));
        return $pdf->download('reporte_'.$persona->nombre.' '.$persona->apellido.'.pdf');
    }

}
