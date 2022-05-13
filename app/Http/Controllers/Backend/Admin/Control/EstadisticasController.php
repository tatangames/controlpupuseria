<?php

namespace App\Http\Controllers\Backend\Admin\Control;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstadisticasController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        // clientes registrados hoy
        $fecha = Carbon::now('America/El_Salvador');
        $clientehoy = Clientes::whereDate('fecha', $fecha)->count();

        // total de clientes
        $clientetotal = Clientes::count();

        return view('backend.admin.estadisticas.vistaestadisticas', compact('clientehoy', 'clientetotal'));
    }
}
