<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntentosCorreo extends Model
{
    use HasFactory;
    protected $table = 'intentos_correo';
    public $timestamps = false;
}
