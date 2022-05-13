<?php

namespace App\Http\Controllers\Backend\Api\Carrito;

use App\Http\Controllers\Controller;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Clientes;
use App\Models\DireccionCliente;
use App\Models\Horario;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\Producto;
use App\Models\Zonas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiCarritoController extends Controller
{
    public function verCarritoDecompras(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
        );


        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->clienteid)->first()){

            try {

                $estadoProductoGlobal = 0; // saver si producto esta activo


                // preguntar si usuario ya tiene un carrito de compras
                if($cart = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
                    $producto = DB::table('producto AS p')
                        ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                        ->select('p.id AS productoID', 'p.nombre', 'c.cantidad',
                            'p.imagen', 'p.precio', 'p.activo', 'p.disponibilidad',
                            'c.id AS carritoid', 'p.utiliza_imagen')
                        ->where('c.carrito_temporal_id', $cart->id)
                        ->get();

                    // verificar cada producto
                    foreach ($producto as $pro) {

                        // verificar si un producto no esta disponible o activo

                        // saver si al menos un producto no esta activo o disponible
                        if($pro->activo == 0 || $pro->disponibilidad == 0){
                            $estadoProductoGlobal = 1; // producto no disponible global
                        }

                        // multiplicar cantidad por el precio de cada producto
                        $precio = $pro->cantidad * $pro->precio;

                        // convertir
                        $valor = number_format((float)$precio, 2, '.', '');

                        $pro->precio = $valor;
                    }

                    // sub total de la orden
                    $subTotal = collect($producto)->sum('precio'); // sumar todos el precio

                    //** validacion de horarios */


                    return [
                        'success' => 1,
                        'subtotal' => number_format((float)$subTotal, 2, '.', ''), // subtotal
                        'estadoProductoGlobal' => $estadoProductoGlobal, // saver si producto esta activo
                        'producto' => $producto, //todos los productos
                    ];

                }else{
                    return [
                        'success' => 2  // no tiene carrito de compras
                    ];
                }
            }catch(\Error $e){
                return [
                    'success' => 3, // error
                ];
            }
        }
        else{
            return ['success' => 4]; // usuario no encontrado
        }
    }

    public function borrarCarritoDeCompras(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }

        if($carrito = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
            CarritoExtra::where('carrito_temporal_id', $carrito->id)->delete();
            CarritoTemporal::where('clientes_id', $request->clienteid)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function borrarProductoDelCarrito(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'carritoid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // verificar si tenemos carrito
        if($ctm = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

            // encontrar el producto a borrar
            if(CarritoExtra::where('id', $request->carritoid)->first()){
                CarritoExtra::where('id', $request->carritoid)->delete();

                // saver si tenemos mas productos aun
                $dato = CarritoExtra::where('carrito_temporal_id', $ctm->id)->get();

                if(count($dato) == 0){
                    CarritoTemporal::where('id', $ctm->id)->delete();
                    return ['success' => 1]; // carrito de compras borrado
                }

                return ['success' => 2]; // producto eliminado
            }else{
                // producto a borrar no encontrado
                return ['success' => 3];
            }
        }else{
            // carrito de compras borrado
            return ['success' => 1 ];
        }
    }


    public function editarCantidadProducto(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'cantidad' => 'required',
            'carritoid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // buscar carrito de compras a quien pertenece el producto
        // verificar si existe el carrito
        if(CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
            // verificar si existe el carrito extra id que manda el usuario
            if(CarritoExtra::where('id', $request->carritoid)->first()){

                CarritoExtra::where('id', $request->carritoid)->update(['cantidad' => $request->cantidad,
                    'nota_producto' => $request->nota]);

                return [
                    'success' => 1 // cantidad actualizada
                ];

            }else{
                // producto no encontrado
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }

    public function verProductoCarritoEditar(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'carritoid' => 'required' //es id del producto
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

            if(CarritoExtra::where('id', $request->carritoid)->first()){

                // informacion del producto + cantidad elegida
                $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                    ->select('p.id AS productoID', 'p.nombre', 'p.descripcion', 'c.cantidad', 'c.nota_producto',
                        'p.imagen', 'p.precio', 'p.utiliza_nota', 'p.nota', 'p.utiliza_imagen')
                    ->where('c.id', $request->carritoid)
                    ->first();

                return [
                    'success' => 1,
                    'producto' => $producto,
                ];

            }else{
                // producto no encontrado
                return ['success' => 2];
            }
        }else{
            // no tiene carrito
            return ['success' => 3];
        }
    }


    public function verOrdenAProcesarCliente(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // verificar que cliente tenga direccion
        if(!DireccionCliente::where('clientes_id', $request->clienteid)->first()){
            // sin direccion
            return ['success' => 1];
        }

        try {
            // preguntar si usuario ya tiene un carrito de compras
            if($cart = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

                $infoDireccion = DireccionCliente::where('clientes_id', $request->clienteid)
                    ->where('seleccionado', 1)
                    ->first();

                $resultado = 0; // sub total del carrito de compras

                // listado de productos del carrito
                $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                    ->select('p.precio', 'c.cantidad')
                    ->where('c.carrito_temporal_id', $cart->id)
                    ->get();

                $pila = array();

                // multiplicar precio x cantidad
                foreach($producto as $p){

                    $cantidad = $p->cantidad;
                    $precio = $p->precio;
                    $multi = $cantidad * $precio;
                    array_push($pila, $multi);
                }

                // sumar listado de sub totales de cada producto multiplicado
                foreach ($pila as $valor){
                    $resultado = $resultado + $valor;
                }

                // precio de la zona servicio
                $infoZona = Zonas::where('id', $infoDireccion->zonas_id)->first();

                // obtiene precio envio de la zona servicio
                // PRIORIDAD 1
                $envioPrecio = $infoZona->precio_envio;

                // PRIORIDAD 2
                // esta zona servicio tiene un minimo de $$ para aplicar nuevo tipo de cargo
                if($infoZona->min_envio == 1){

                    // verificar resultado que es el sub total
                    if($resultado >= $infoZona->precio_minimo){
                        //aplicar nuevo tipo cargo
                        $envioPrecio = 0;
                    }
                }

                // sumar a total
                $total = $resultado + $envioPrecio;

                $total = number_format((float)$total, 2, '.', '');
                $envioPrecio = number_format((float)$envioPrecio, 2, '.', '');

                return [
                    'success' => 2,
                    'total' => $total,
                    'subtotal' => number_format((float)$resultado, 2, '.', ''),
                    'envio' => $envioPrecio,
                    'direccion' => $infoDireccion->direccion,
                ];

            }else{
                // no tiene carrito de compras
                return ['success' => 3];
            }
        }catch(\Error $e){
            return ['success' => 4, 'err' => $e];
        }
    }


    public function procesarOrdenEstado1(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required',
            'version' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }


        // verificar que cliente tenga direccion
        if(!DireccionCliente::where('clientes_id', $request->clienteid)->first()){
            // sin direccion
            return ['success' => 1];
        }


        DB::beginTransaction();

        try {

            // informacion de la direccion del cliente
            $infoDireccion = DireccionCliente::where('clientes_id', $request->clienteid)
                ->where('seleccionado', 1)
                ->first();

            //**** VALIDACIONES

            // validacion de horarios para este servicio
            $numSemana = [
                0 => 1, // domingo
                1 => 2, // lunes
                2 => 3, // martes
                3 => 4, // miercoles
                4 => 5, // jueves
                5 => 6, // viernes
                6 => 7, // sabado
            ];

            // hora y fecha
            $getValores = Carbon::now('America/El_Salvador');
            $getDiaHora = $getValores->dayOfWeek;
            $diaSemana = $numSemana[$getDiaHora];
            $hora = $getValores->format('H:i:s');

            // verificar sin la segunda hora
            $horario = Horario::where('dia', $diaSemana)
                ->where('hora1', '<=', $hora)
                ->where('hora2', '>=', $hora)
                ->get();

            if(count($horario) >= 1){
                // abierto
            }else{
                // cerrado horario normal del servicio (2 horarios)
                return ['success' => 2, 'msj1' => "El negocio esta cerrado por el momento"];
            }

            // preguntar si este dia esta cerrado
            $cerradoHoy = Horario::where('dia', $diaSemana)->first();

            if($cerradoHoy->cerrado == 1){
                // cerrado este dia el negocio
                return ['success' => 3, 'msj1' => "este dia tenemos cerrado"];
            }

            $infoZona = Zonas::where('id', $infoDireccion->zonas_id)->first();

            if($infoZona->saturacion == 1){
                // zona bloqueada por algun problema
                return ['success' => 4, 'msj1' => $infoZona->mensaje_bloqueo];
            }

            // horario delivery para esa zona
            $horarioDeliveryZona = Zonas::where('id', $infoDireccion->zonas_id)
                ->where('hora_abierto_delivery', '<=', $hora)
                ->where('hora_cerrado_delivery', '>=', $hora)
                ->get();

            if(count($horarioDeliveryZona) >= 1){
                // abierto
            }else{
                // cerrado horario de zona
                return ['success' => 5, 'msj1' => "temporalmente cerrado para esta zona el envio"];
            }


            // preguntar si usuario ya tiene un carrito de compras
            if($cart = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

                $resultado = 0; // sub total del carrito de compras

                // listado de productos del carrito
                $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                    ->select('p.precio', 'c.cantidad', 'p.id', 'c.nota_producto')
                    ->where('c.carrito_temporal_id', $cart->id)
                    ->get();

                $pila = array();

                // multiplicar precio x cantidad
                foreach($producto as $p){

                    $cantidad = $p->cantidad;
                    $precio = $p->precio;
                    $multi = $cantidad * $precio;
                    array_push($pila, $multi);
                }

                // sumar listado de sub totales de cada producto multiplicado
                foreach ($pila as $valor){
                    $resultado = $resultado + $valor;
                }

                // precio de la zona servicio
                $infoZona = Zonas::where('id', $infoDireccion->zonas_id)->first();

                // obtiene precio envio de la zona servicio
                // PRIORIDAD 1
                $envioPrecio = $infoZona->precio_envio;

                // PRIORIDAD 2
                // esta zona servicio tiene un minimo de $$ para aplicar nuevo tipo de cargo
                if($infoZona->min_envio == 1){

                    // verificar resultado que es el sub total
                    if($resultado >= $infoZona->precio_minimo){
                        //aplicar nuevo tipo cargo
                        $envioPrecio = 0;
                    }
                }

                $fechahoy = Carbon::now('America/El_Salvador');

                $idOrden = DB::table('ordenes')->insertGetId(
                    [ 'clientes_id' => $request->clienteid,
                        'nota' => $request->nota,
                        'cambio' => $request->cambio,
                        'fecha_orden' => $fechahoy,
                        'precio_consumido' => $resultado,
                        'precio_envio' => $envioPrecio,

                        'estado_2' => 0,
                        'fecha_2' => null,
                        'hora_2' => 0,

                        'estado_3' => 0,
                        'fecha_3' => null,

                        'estado_4' => 0,
                        'fecha_4' => null,

                        'estado_5' => 0,
                        'fecha_5' => null,

                        'estado_6' => 0,
                        'fecha_6' => null,

                        'estado_7' => 0,
                        'fecha_7' => null,

                        'estado_8' => 0,
                        'fecha_8' => null,
                        'mensaje_8' => null,

                        'visible' => 1,
                        'visible_p' => 1,
                        'visible_p2' => 0,
                        'visible_p3' => 0,
                        'visible_m' => 0,
                        'cancelado' => 0, // 0: nadie, 1: cliente, 2 propietarios
                    ]
                );

                // guadar todos los productos de esa orden
                foreach($producto as $p){

                    $data = array('ordenes_id' => $idOrden,
                        'producto_id' => $p->id,
                        'cantidad' => $p->cantidad,
                        'precio' => $p->precio,
                        'nota' => $p->nota_producto);
                    OrdenesDescripcion::insert($data);
                }

                $nuevaDir = new OrdenesDirecciones();
                $nuevaDir->clientes_id = $request->clienteid;
                $nuevaDir->ordenes_id = $idOrden;
                $nuevaDir->zonas_id = $infoZona->id;
                $nuevaDir->nombre = $infoDireccion->nombre;
                $nuevaDir->direccion = $infoDireccion->direccion;
                $nuevaDir->numero_casa = $infoDireccion->numero_casa;
                $nuevaDir->punto_referencia = $infoDireccion->punto_referencia;
                $nuevaDir->latitud = $infoDireccion->latitud;
                $nuevaDir->longitud = $infoDireccion->longitud;
                $nuevaDir->latitudreal = $infoDireccion->latitudreal;
                $nuevaDir->longitudreal = $infoDireccion->longitudreal;
                $nuevaDir->version = $request->version;
                $nuevaDir->save();

                // BORRAR CARRITO TEMPORAL DEL USUARIO

                //CarritoExtra::where('carrito_temporal_id', $cart->id)->delete();
                //CarritoTemporal::where('clientes_id', $request->clienteid)->delete();

                DB::commit();
                return ['success' => 6];
            }else{
                // no tiene carrito de compras
                return ['success' => 7];
            }

        } catch(\Throwable $e){
            DB::rollback();
            return [
                'success' => 101,
                'message' => "e".  $e
            ];
        }
    }




}
