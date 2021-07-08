<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoActividad extends Model
{
    protected $table = 'estado_actividad';
    protected $fillable = ['descripcion','color'];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
}
