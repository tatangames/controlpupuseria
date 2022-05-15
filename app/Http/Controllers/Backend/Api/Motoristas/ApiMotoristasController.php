<?php

namespace App\Http\Controllers\Backend\Api\Motoristas;

use App\Http\Controllers\Controller;
use App\Models\Motoristas;
use App\Models\MotoristasOrdenes;
use App\Models\Ordenes;
use App\Models\OrdenesDirecciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiMotoristasController extends Controller
{
    public function loginMotorista(Request $request){
        $rules = array(
            'usuario' => 'required',
            'password' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Motoristas::where('usuario', $request->usuario)->first()){

            if($p->activo == 0){
                return ['success' => 1]; // motorista no activo
            }

            if (Hash::check($request->password, $p->password)) {

                $id = $p->id;
                if($request->token_fcm != null){
                    Motoristas::where('id', $p->id)->update(['token_fcm' => $request->token_fcm]);
                }

                // disponible
                Motoristas::where('id', $p->id)->update(['disponible' => 1]);

                return ['success' => 2, 'id' => $id];
            }
            else{
                return ['success' => 3];
            }
        }else{
            return ['success' => 4];
        }
    }

    public function verNuevasOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($m = Motoristas::where('id', $request->id)->first()){

            if($m->activo == 0){
                return ['success' => 1];
            }

            $moto = Motoristas::where('id', $request->id)->get();

            $noquiero = DB::table('motoristas_ordenes AS mo')->get();

            $pilaOrden = array();
            foreach($noquiero as $p){
                array_push($pilaOrden, $p->ordenes_id);
            }

            $orden = Ordenes::where('estado_5', 0)
                ->where('estado_3', 1) // inicia la orden
                ->where('estado_7', 0) // orden no cancelada
                ->whereNotIn('id', $pilaOrden)
                ->get();

            foreach($orden as $o){

                $infoDireccion = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
                $o->cliente = $infoDireccion->nombre;
                $o->direccion = $infoDireccion->direccion;
                $o->telefono = $infoDireccion->telefono;

                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
            }

            return ['success' => 2, 'ordenes' => $orden];
        }else{
            return ['success' => 3];
        }
    }

    public function verOrdenPorID(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($info = Ordenes::where('id', $request->ordenid)->first()){

            //sacar direccion de la orden
            $orden = OrdenesDirecciones::where('ordenes_id', $request->ordenid)->get();

            $venta = number_format((float)$info->precio_consumido, 2, '.', ',');

            return ['success' => 1, 'cliente' => $orden, 'venta' => $venta];
        }else{
            return ['success' => 2];
        }
    }

    public function verProductosOrden(Request $request){
        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $producto = DB::table('ordenes AS o')
                ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                ->select('od.id AS productoID', 'p.nombre', 'od.nota',
                    'p.imagen', 'p.utiliza_imagen', 'od.precio', 'od.cantidad')
                ->where('o.id', $request->ordenid)
                ->get();

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', ',');
            }
            return ['success' => 1, 'productos' => $producto];
        }else{
            return ['success' => 3];
        }
    }

    public function obtenerOrden(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required',
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            if($or = Ordenes::where('id', $request->ordenid)->first()){

                DB::beginTransaction();
                try {
                    if(MotoristasOrdenes::where('ordenes_id', $request->ordenid)->first()){
                        // orden ya lo tiene un motorista
                        return ['success' => 1];
                    }

                    if($or->estado_7 == 1){
                        // orden cancelada
                        return ['success' => 2];
                    }

                    // ACTUALIZAR
                    Ordenes::where('id', $request->ordenid)->update(['visible_m' => 1]);
                    $fecha = Carbon::now('America/El_Salvador');

                    $nueva = new MotoristasOrdenes();
                    $nueva->ordenes_id = $or->id;
                    $nueva->motoristas_id = $request->id;
                    $nueva->fecha_agarrada = $fecha;
                    $nueva->save();

                    DB::commit();

                    return ['success' => 3];

                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 5];
                }

            }else{
                return ['success' => 5]; // orden no encontrada
            }
        }else{
            return ['success' => 5]; // motorista no encontrado
        }
    }


    public function verProcesoOrdenes(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            // mostrar si fue cancelada para despues setear visible_m

            $orden = DB::table('motoristas_ordenes AS mo')
                ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                ->select('o.id', 'o.precio_consumido', 'o.fecha_4', 'o.hora_2',
                    'o.estado_5', 'o.estado_6', 'o.precio_envio',
                    'o.estado_7', 'o.visible_m', 'o.nota')
                ->where('o.estado_6', 0) // aun sin entregar al cliente
                ->where('o.visible_m', 1) // para ver si una orden fue cancelada a los 10 minutos, y el motorista la agarro, asi ver el estado
                ->where('o.estado_4', 0) // aun no han salido a entregarse
                ->where('mo.motoristas_id', $request->id)
                ->get();

            // sumar mas envio
            foreach($orden as $o) {

                // buscar metodo de pago
                $infoOrdenes = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $suma = $o->precio_consumido + $o->precio_envio;
                $o->precio_consumido = number_format((float)$suma, 2, '.', ',');
                $o->precio_envio = number_format((float)$o->precio_envio, 2, '.', ',');


            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }


    public function verOrdenProcesoPorID(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            //sacar direccion de la orden

            $orden = OrdenesDirecciones::where('ordenes_id', $request->ordenid)->get();

            // titulo que dira la notificacion, cuando se alerte al cliente que esta llegando su pedido.
            $mensaje = "Su orden #" . $request->ordenid . " esta llegando";

            return ['success' => 1, 'cliente' => $orden,
                'estado' => $or->estado_6,
                'cancelado' => $or->estado_7,
                'mensaje' => $mensaje];
        }else{
            return ['success' => 2];
        }
    }

    public function iniciarEntrega(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            if($or->estado_7 == 1){
                return ['success' => 1];
            }
            // orden ya fue preparada por el propietario
            if($or->estado_3 == 1 && $or->estado_5 == 0){

                $fecha = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $request->ordenid)->update(['estado_4' => 1,
                    'fecha_4' => $fecha]);

                return ['success' => 2]; //orden va en camino
            }else{
                return ['success' => 3]; // la orden aun no ha sido preparada
            }
        }else{
            return ['success' => 4];
        }
    }

    public function informacionDisponibilidad(Request $request){
        $rules = array(
            'id' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Motoristas::where('id', $request->id)->first()){

            return ['success'=> 1, 'disponibilidad' => $p->disponible];
        }else{
            return ['success'=> 2];
        }
    }

    public function modificarDisponibilidad(Request $request){
        $rules = array(
            'id' => 'required',
            'valor' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            Motoristas::where('id', $request->id)->update(['disponible' => $request->valor]);

            return ['success'=> 1];
        }else{
            return ['success'=> 2]; // motorista no encontrado
        }
    }

    public function informacionCuenta(Request $request){
        $rules = array(
            'id' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Motoristas::where('id', $request->id)->first()){

            return ['success'=> 1, 'nombre' => $p->nombre];
        }else{
            return ['success'=> 2];
        }
    }

    public function actualizarPassword(Request $request){
        $rules = array(
            'id' => 'required',
            'password' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            Motoristas::where('id', $request->id)->update(['password' => Hash::make($request->password)]);

            return ['success'=> 1];
        }else{
            return ['success'=> 2];
        }
    }

    public function verProcesoOrdenesEntrega(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            // mostrar si fue cancelada para despues setear visible_m

            $orden = DB::table('motoristas_ordenes AS mo')
                ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                ->select('o.id', 'o.precio_consumido', 'o.fecha_4', 'o.hora_2',
                    'o.estado_5', 'o.estado_6', 'o.precio_envio','o.estado_7', 'o.visible_m',
                    'o.nota')
                ->where('o.estado_5', 0) // aun sin entregar al cliente
                ->where('o.visible_m', 1) // para ver si una orden fue cancelada a los 10 minutos, y el motorista la agarro, asi ver el estado
                ->where('o.estado_5', 1) // van a entregarse
                ->where('mo.motoristas_id', $request->id)
                ->get();

            // sumar mas envio
            foreach($orden as $o){
                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                $total = $o->precio_consumido - $o->precio_envio;
                $o->total = number_format((float)$total, 2, '.', ',');
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }
}
