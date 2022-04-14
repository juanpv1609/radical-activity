<?php

namespace App\Models;

use App\Models\User;
use App\Models\Actividad;
use App\Models\Clasificacion;
use App\Models\TipoActividad;
use App\Models\EstadoActividad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Actividades extends Model
{
    protected $table = 'actividades';
    protected $fillable = [
        'tipo_actividad',
        'clasificacion',
        'cliente',
        'descripcion',
        'dia',
        'h_inicio',
        'h_fin',
        'observacion',
        'colaboradores',
        'estado',
        'verificada',

        'is_verified_by'
    ];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'dia', 'id');
    }
    public function tipo()
    {
        return $this->belongsTo(TipoActividad::class, 'tipo_actividad', 'id');
    }
    public function clasif() //tabla clasificacion
    {
        return $this->belongsTo(Clasificacion::class, 'clasificacion', 'id');
    }
    public function status()
    {
        return $this->belongsTo(EstadoActividad::class, 'estado', 'id');
    }
    public function verificador()
    {
        return $this->belongsTo(User::class, 'is_verified_by', 'id');
    }
}
