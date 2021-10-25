<?php

namespace App\Models;

use App\Models\PerfilPuesto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $fillable = ['nombre',
                            'descripcion',
                            'inicio',
                            'fin',
                            'perfil_puesto',
                            'estado'];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
    public function cargo(){
    	return $this->belongsTo(PerfilPuesto::class, 'perfil_puesto', 'id');
    }
}
