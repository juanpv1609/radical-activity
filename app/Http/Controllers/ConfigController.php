<?php

namespace App\Http\Controllers;

use App\Models\EstadoEstudio;
use App\Models\TipoIdentificacion;

class ConfigController extends Controller
{

    public function identificacion()
    {
        $tipo_identificacion = TipoIdentificacion::all()->toArray();

        return $tipo_identificacion;
    }
    public function estadoEstudio()
    {
        $estado = EstadoEstudio::all()->toArray();

        return $estado;
    }

}
