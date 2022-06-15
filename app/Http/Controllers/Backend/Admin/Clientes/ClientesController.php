<?php

namespace App\Http\Controllers\Backend\Admin\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\DireccionCliente;
use App\Models\Zonas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function indexRegistradosHoy(){

        $dataFecha = Carbon::now('America/El_Salvador');
        $fecha = date("d-m-Y", strtotime($dataFecha));
        return view('backend.admin.cliente.hoy.vistaHoy', compact('fecha'));
    }

    public function tablaRegistradosHoy(){

        $fecha = Carbon::now('America/El_Salvador');
        $cliente = Clientes::whereDate('fecha', $fecha)->get();

        foreach($cliente as $c){
            $c->fecha = date("h:i A", strtotime($c->fecha));
        }

        return view('backend.admin.cliente.hoy.tablaHoy', compact('cliente'));
    }

    public function indexListaClientes(){
        return view('backend.admin.cliente.listado.vistaListado');
    }

    public function tablaindexListaClientes(){

        $lista = Clientes::orderBy('fecha', 'DESC')->get();

        foreach($lista as $c){
            $c->fecha = date("d-m-Y h:i A", strtotime($c->fecha));
        }

        return view('backend.admin.cliente.listado.tablaListado', compact('lista'));
    }

    public function informacionCliente(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($cliente = Clientes::where('id', $request->id)->first()){
            return ['success' => 1, 'cliente' => $cliente];
        }else{
            return ['success' => 2];
        }
    }

    public function actualizarCliente(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            Clientes::where('id', $request->id)->update(['activo' => $request->cbactivo]);

            if($request->cbpassword == 1){
                Clientes::where('id', $request->id)->update(['password' => bcrypt('12345678')]);
            }

            return ['success'=>1];
        }else{
            return ['success'=>2];
        }
    }

    public function indexListaDirecciones($id){

        $usuario = Clientes::where('id', $id)->pluck('usuario')->first();

        return view('backend.admin.cliente.direcciones.vistaDireccion', compact('id', 'usuario'));
    }

    public function tablaIndexListaDirecciones($id){

        $lista = DireccionCliente::where('clientes_id', $id)
            ->orderBy('nombre')
            ->get();

        foreach ($lista as $ll){
            $infoZona = Zonas::where('id', $ll->zonas_id)->first();
            $ll->zona = $infoZona->nombre;
        }

        return view('backend.admin.cliente.direcciones.tablaDireccion', compact('id', 'lista'));
    }


}
