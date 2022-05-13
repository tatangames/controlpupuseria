<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloquesEventos extends Model
{
    use HasFactory;
    protected $table = 'bloques_eventos';
    public $timestamps = false;
}
