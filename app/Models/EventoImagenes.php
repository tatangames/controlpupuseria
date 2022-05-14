<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoImagenes extends Model
{
    use HasFactory;
    protected $table = 'evento_imagenes';
    public $timestamps = false;

    protected $fillable = [
        'posicion',
    ];
}
