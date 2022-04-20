<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clasificacion extends Model
{
    protected $table = 'clasificacion';
    protected $fillable = ['nombre',
                            'descripcion',
                            'color',
                            'estado',
                        'is_deleted'];
    public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
}
