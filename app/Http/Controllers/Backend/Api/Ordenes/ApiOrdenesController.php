<?php

namespace App\Http\Controllers\Backend\Api\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\Ordenes;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiOrdenesController extends Controller
{
    public function ordenesActivas(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->clienteid)->first()){
            $orden = Ordenes::where('clientes_id', $request->clienteid)
                ->where('visible', 1)
                ->orderBy('id', 'DESC')
                ->get();

            foreach($orden as $o){
                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));

                $infoDireccion = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $o->direccion = $infoDireccion->direccion;

                $sumado = $o->precio_consumido + $o->precio_envio;
                $sumado = number_format((float)$sumado, 2, '.', '');
                $o->total = $sumado;
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }

    public function estadoOrdenesActivas(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $orden = Ordenes::where('id', $request->ordenid)->get();

            foreach($orden as $o){

                if($o->estado_2 == 1){ // propietario inicia la orden
                   $o->fecha_2 = date("h:i A d-m-Y", strtotime($o->fecha_2));
                }

                if($o->estado_4 == 1){ // motorista inicia la entrega
                    $o->fecha_4 = date("h:i A d-m-Y", strtotime($o->fecha_4));
                }

                if($o->estado_5 == 1){ // motorista termina la entrega
                    $o->fecha_5 = date("h:i A d-m-Y", strtotime($o->fecha_5));
                }

                if($o->estado_6 == 1){ // cliente finaliza la entrega
                    $o->fecha_6 = date("h:i A d-m-Y", strtotime($o->fecha_6));
                }

                if($o->estado_7 == 1){ // la orden fue cancelada, 1 cliente, 2 propietario
                    $o->fecha_7 = date("h:i A d-m-Y", strtotime($o->fecha_7));
                }

                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }

    public function cancelarOrdenCliente(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($orden = Ordenes::where('id', $request->ordenid)->first()){

            if($orden->estado_7 == 0){

                // seguro para evitar cancelar cuando servicio inicia a preparar orden
                if($orden->estado_2 == 1){
                    return ['success' => 1];
                }

                DB::beginTransaction();

                try {

                    $fecha = Carbon::now('America/El_Salvador');
                    Ordenes::where('id', $request->ordenid)->update(['estado_7' => 1,
                        'cancelado' => 1,
                        'visible' => 0,
                        'fecha_7' => $fecha]);

                    DB::commit();
                    return ['success' => 2];

                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 3];
                }

            }else{
                return ['success' => 2]; // ya cancelada
            }
        }else{
            return ['success' => 3]; // no encontrada
        }
    }

    public function listadoProductosOrdenes(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){
            $producto = DB::table('ordenes AS o')
                ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                ->select('od.id AS productoID', 'p.nombre', 'p.utiliza_imagen', 'p.imagen', 'od.precio', 'od.cantidad')
                ->where('o.id', $request->ordenid)
                ->get();

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', '');
            }

            return ['success' => 1, 'productos' => $producto];
        }else{
            return ['success' => 2];
        }
    }

    public function listadoProductosOrdenesIndividual(Request $request){

        $reglaDatos = array(
            'productoid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(OrdenesDescripcion::where('id', $request->productoid)->first()){

            $producto = DB::table('ordenes_descripcion AS o')
                ->join('producto AS p', 'p.id', '=', 'o.producto_id')
                ->select('p.imagen', 'p.nombre', 'p.descripcion', 'p.utiliza_imagen', 'o.precio', 'o.cantidad', 'o.nota')
                ->where('o.id', $request->productoid)
                ->get();

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', '');
            }

            return ['success' => 1, 'productos' => $producto];
        }else{
            return ['success' => 2];
        }
    }

}
