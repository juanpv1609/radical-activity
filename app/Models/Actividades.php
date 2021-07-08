<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividades extends Model
{
    protected $table = 'actividades';
    protected $fillable = [
        'tipo_actividad',
        'descripcion',
        'dia',
        'h_inicio',
        'h_fin',
        'observacion',
        'colaboradores',
        'estado',
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
    public function status()
    {
        return $this->belongsTo(EstadoActividad::class, 'estado', 'id');
    }
}
