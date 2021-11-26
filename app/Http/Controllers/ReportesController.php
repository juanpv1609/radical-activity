<?php

namespace App\Http\Controllers;

use PDF;
use DateTime;
use DateInterval;
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
            array_push($dataCertificaciones, $auxCertificaciones);
        }

        $pdf = App::make('dompdf.wrapper');

        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->loadView('pdf.certificaciones', compact('dataCertificaciones'));
        return $pdf->download('reporteCertificaciones.pdf');
    }
    public function reporteActividadesContable($dates, $users)
    {

        $usuarios = explode(",", $users);
        $fechas = explode(",", $dates);
        $dataUsers = User::whereIn('id', $usuarios)
                            ->orderBy('name')->get();
        $arrayUsers=[];
        $usuario;
        $personas=[];
        $array_h_inicio=[];
        $array_h_fin=[];
        $h_inicioNP;
        $h_finNP;
 /**
  * !CONTROLAR HORARIO DE 22:00 A 06:00 se suma 16 horas en lugar de 8 <- REALIZADO
  * ? Estatus OK
  */

        foreach ($dataUsers as $user) {
            $total_horas=0.0;
            $usuario['nombre']=$user->name;
            $usuario['horas_p']=[];
            $horas_p=[];
            $horas_np=[];
            foreach ($fechas as $fecha) {
                $hora_p='';
                $hora_t=0.0;
                $actividad = Actividad::with('usuario', 'horario')
                    ->where('usuario_id', $user->id)
                    ->where('fecha', $fecha)
                    ->first();
                    //dd($actividad);

                    if (($actividad)) { //si tiene la actividad en dicha fecha
                        $tipo_horario = $actividad['horario_id'];
                    $cond=[
                        ['dia','=', $actividad->id],
                        ['tipo_actividad','<>', 6],
                        ];

                    $actividades = Actividades::where($cond)->get();
                    $actividades_np = Actividades::where([['dia', $actividad->id],['tipo_actividad',6]])->first();
                    //dd($actividades_np);
                    if (count($actividades)>0) {
                        # code...
                        foreach ($actividades as $item) {
                            array_push($array_h_inicio, $item->h_inicio);
                            array_push($array_h_fin, $item->h_fin);

                        }
                        $horaInicio = ($tipo_horario!=3) ? new DateTime(min($array_h_inicio)) : new DateTime('00:00:00'); //obtengo el menor valor
                        $horaTermino =  new DateTime(max($array_h_fin)); //obtengo el mayor valor
                        $array_h_inicio=[];
                        $array_h_fin=[];


                    }else if (count($actividades)==1){
                        $horaInicio = ($tipo_horario!=3) ? new DateTime(($actividades->h_inicio)->format('%H:%i')) : new DateTime('00:00:00'); //obtengo el menor valor
                        $horaTermino = new DateTime(($actividades->h_fin)->format('%H:%i')); //obtengo el mayor valor

                    }else{
                        $horaInicio = new DateTime('00:00:00'); //obtengo el menor valor
                        $horaTermino = new DateTime('00:00:00'); //obtengo el mayor valor

                    }
                    if ($actividades_np) {
                        $h_inicioNP = $actividades_np->h_inicio;
                        $h_finNP = $actividades_np->h_fin;

                    }else{
                        $h_inicioNP = '00:00:00';
                        $h_finNP = '00:00:00';

                    }

                    //dd($array_h_inicioNP);


                    $interval = ($tipo_horario!=3) ? $horaInicio->diff($horaTermino) : $horaInicio->diff($horaTermino->add(new DateInterval('PT2H'))); //resta

                    $horaInicioNP = new DateTime($h_inicioNP); //obtengo el menor valor
                    $horaTerminoNP = new DateTime($h_finNP); //obtengo el mayor valor
                    $intervalNP = $horaInicioNP->diff($horaTerminoNP); //resta


                    $nuevaFecha = new DateTime($interval->format('%H:%i'));
                    $total_horas = new DateTime($interval->format('%H:%i'));

                    $nuevaFecha->sub($intervalNP);
                    //$total_horas+=$nuevaFecha;
                    $hora_p=$nuevaFecha->format('H:i');

                }else{
                    $hora_t=0.0;
                    $hora_p=gmdate('H:i', floor(($hora_t) * 3600));
                    $hora_p='--:--';

                    $total_horas='00:00';

                }
                 array_push($horas_p, $hora_p);
                /*
                    $total_horas+=$hora_t;
                foreach ($horas_p as $item) {
                    $hms = explode(":", $item);
                    //dd($hms);

                    $total_horas+= $hms[0]+($hms[1]/60);

                } */

            }
           // $usuario['total']=gmdate('H:i', floor(($total_horas) * 3600));
            $usuario['horas_p']=$horas_p;

            array_push($arrayUsers, $usuario);


        }
        return($arrayUsers);
    }
    public function reporteActividadesContable2($dates, $users)
    {

        $usuarios = explode(",", $users);
        $fechas = explode(",", $dates);
        $dataUsers = User::whereIn('id', $usuarios)
                            ->get();
        $arrayUsers=[];
        $usuario;
        $personas=[];


        foreach ($dataUsers as $user) {
            $total_horas=0.0;
            $usuario['nombre']=$user->name;
            $usuario['horas_p']=[];
            $horas_p=[];
            $horas_np=[];
            foreach ($fechas as $fecha) {
                $hora_p='';
                $hora_t=0.0;
                $actividad = Actividad::with('usuario', 'horario')
                    ->where('usuario_id', $user->id)
                    ->where('fecha', $fecha)
                    ->first();
                if (($actividad)) { //si tiene la actividad en dicha fecha
                    $hora_t=$actividad->horas_p-$actividad->horas_np;
                    $hora_p=gmdate('H:i', floor(($hora_t) * 3600));
                }else{
                    $hora_t=0.0;
                    $hora_p=gmdate('H:i', floor(($hora_t) * 3600));
                }
                $total_horas+=$hora_t;
                array_push($horas_p, $hora_p);

            }
            $usuario['total']=gmdate('H:i', floor(($total_horas) * 3600));
            $usuario['horas_p']=$horas_p;

            array_push($arrayUsers, $usuario);
        }
        return($arrayUsers);
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
