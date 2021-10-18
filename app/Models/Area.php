<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $table = 'areas';
    protected $fillable = ['nombre',
                            'descripcion',
                            'estado','is_deleted'];
    //public $timestamps = false;
    protected $guarded = [];
}