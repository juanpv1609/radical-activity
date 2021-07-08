<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoActividad extends Model
{
    protected $table = 'tipo_actividad';
    protected $fillable = [
                            'descripcion',
                            'estado',
                            'is_deleted',];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
}
