<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panet extends Model
{
    protected $table = 'panet_acciones';
    protected $fillable = ['usuario',
                            'accion',
                            'cliente',
                            'count_cierre',
                            ];
    //public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
}
