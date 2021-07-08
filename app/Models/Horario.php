<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $fillable = ['nombre',
                            'descripcion',
                            'inicio',
                            'fin',
                            'estado'];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
}
