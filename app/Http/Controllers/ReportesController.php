<?php

namespace App\Http\Controllers;

use App\Models\Certificaciones;
use App\Models\PersonaEstudio;
use PDF;
use Illuminate\Http\Request;
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
            $auxCertificaciones = array(
                'certificacion' => $item->solucion,
                'total' => count($personaEstudio),
                'personas' => $personas
            );
            array_push($dataCertificaciones,$auxCertificaciones);
            //array_push($dataCertificaciones['certificacion'], $personas);

            //$dataCertificaciones['certificacion']= $personas;
        }
        //$dataCertificaciones = $aux;
        //dd($dataCertificaciones);



        $pdf = App::make('dompdf.wrapper');

        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->loadView('pdf.certificaciones', compact('dataCertificaciones'));
        return $pdf->download('reporteCertificaciones.pdf');
    }

}
