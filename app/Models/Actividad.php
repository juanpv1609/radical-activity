<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Actividad extends Model
{
    protected $table = 'actividad';
    protected $fillable = ['usuario_id',
                            'horario_id',
                            'fecha',
                            'horas_p',
                            'horas_np',
                            'destinatarios',
                            ];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id', 'id');
    }
}
