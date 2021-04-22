<?php

namespace App\Models;

use App\Models\Pais;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Persona extends Model
{
    protected $table = 'persona';
    protected $fillable = [
                            'ci',
                            'nombre',
                            'apellido',
                            'email',
                            'pais_id',
                            'nacionalidad_id',
                            'telefono',
                            'fecha_nacimiento',
                            'estado',
                            'foto',
                            'is_deleted',
                        ];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
    public function pais(){
    	return $this->belongsTo(Pais::class, 'pais_id', 'id');
    }
    public function nacionalidad(){
    	return $this->belongsTo(Pais::class, 'pais_id', 'id');
    }
}
