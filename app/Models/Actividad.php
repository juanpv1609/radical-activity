<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividad';
    protected $fillable = ['usuario',
                            'horario',
                            'fecha',
                            'destinatarios',
                            ];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario', 'id');
    }
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario', 'id');
    }
}
