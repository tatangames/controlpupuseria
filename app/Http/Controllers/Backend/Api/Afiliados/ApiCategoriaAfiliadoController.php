<?php

namespace App\Http\Controllers\Backend\Api\Afiliados;

use App\Http\Controllers\Controller;
use App\Models\Afiliados;
use App\Models\Categorias;
use App\Models\Ordenes;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiCategoriaAfiliadoController extends Controller
{
    public function informacionCategoriasPosiciones(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Afiliados::where('id', $request->id)->first()){

            $categorias = Categorias::orderBy('posicion', 'ASC')->get();

            return ['success'=> 1, 'categorias'=> $categorias];
        }else{
            return ['success'=> 2];
        }
    }


    public function guardarPosicionCategorias(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Afiliados::where('id', $request->id)->first()){

            foreach($request->categoria as $key => $value){

                $posicion = $value['posicion'];

                Categorias::where('id', $key)->update(['posicion' => $posicion]);
            }

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function actualizarDatosCategoria(Request $request){

        $rules = array(
            'id' => 'required',
            'idcategoria' => 'required',
            'nombre' => 'required',
            'valor' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Afiliados::where('id', $request->id)->first()){

            if($request->valor == 1){
                // obtener todos los productos de esa categoria
                $pL = Producto::where('categorias_id', $request->idcategoria)->get();

                $bloqueo = true;

                foreach($pL as $lista){
                    if($lista->disponibilidad == 1){ // si hay al menos 1 producto activo, no se desactiva categoria
                        $bloqueo = false;
                    }
                }

                if($bloqueo){
                    $mensaje = "Para activar la categoría, se necesita un producto disponible";
                    return ['success' => 1, 'msj1' => $mensaje];
                }
            }

            // actualizar
            Categorias::where('id', $request->idcategoria)->update(['activo' => $request->valor, 'nombre' => $request->nombre]);

            return ['success'=> 2];
        }else{
            return ['success'=> 0];
        }
    }

    public function listadoProductoPosicion(Request $request){

        $rules = array(
            'id' => 'required',
            'idcategoria' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Afiliados::where('id', $request->id)->first()){

            // buscar lista de productos
            $categorias = Producto::where('categorias_id', $request->idcategoria)
                ->orderBy('posicion', 'ASC')
                ->where('activo', 1) // activo producto por admin
                ->get();

            return ['success'=> 1, 'categorias'=> $categorias];
        }else{
            return ['success'=> 2];
        }
    }

    public function actualizarProductosPosicion(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Afiliados::where('id', $request->id)->first()){
            foreach($request->categoria as $key => $value){

                $posicion = $value['posicion'];

                Producto::where('id', $key)->update(['posicion' => $posicion]);
            }
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function listadoCategoriasProducto(Request $request){

        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Afiliados::where('id', $request->id)->first()){

            $lista = Categorias::where('visible', 1)
                ->orderBy('posicion', 'ASC')
                ->get();

            return ['success' => 1, 'categorias' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function listadoCategoriasProductoListado(Request $request){

        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Categorias::where('id', $request->id)->first()){

            $lista = Producto::where('categorias_id', $request->id)
                ->orderBy('posicion', 'ASC')
                ->get();

            return ['success' => 1, 'productos' => $lista];
        }else{
            return ['success' => 2];
        }
    }


    public function informacionProductoIndividual(Request $request){

        $rules = array(
            'productoid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Producto::where('id', $request->productoid)->first()){

            $producto = Producto::where('id', $request->productoid)->get();

            return ['success'=> 1, 'productos' => $producto];

        }else{
            return ['success'=> 2];
        }
    }

    public function actualizarProducto(Request $request){
        $rules = array(
            'id' => 'required',
            'productoid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($pp = Afiliados::where('id', $request->id)->first()){

            if(Producto::where('id', $request->productoid)->first()){

                Producto::where('id', $request->productoid)->update(['nombre' => $request->nombre,
                    'descripcion' => $request->descripcion, 'precio' => $request->precio,
                    'nota' => $request->nota, 'disponibilidad' => $request->estadodisponible,
                    'utiliza_nota' => $request->estadonota]);

                return ['success'=> 1];

            }else{
                return ['success'=> 3];
            }
        }else{
            return ['success'=> 3];
        }
    }


    public function informacionEstadoNuevaOrden(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $orden = Ordenes::where('id', $request->ordenid)->get();

            foreach($orden as $o){

                if($o->estado_7 == 1){
                    $o->fecha_7 = date("d-m-Y h:i A", strtotime($o->fecha_7));
                }

                $o->fecha_orden = date("d-m-Y h:i A", strtotime($o->fecha_orden));
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }

    public function listadoProductosOrden(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // buscar la orden
        if(Ordenes::where('id', $request->ordenid)->first()){
            $producto = DB::table('ordenes AS o')
                ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                ->select('od.id AS productoID', 'p.nombre', 'od.nota', 'p.utiliza_imagen', 'p.imagen', 'od.precio', 'od.cantidad')
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

    public function listaOrdenProductoIndividual(Request $request){

        $reglaDatos = array(
            'ordenesid' => 'required' // id tabla orden_descripcion
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // producto descripcion
        if(OrdenesDescripcion::where('id', $request->ordenesid)->first()){

            $producto = DB::table('ordenes_descripcion AS o')
                ->join('producto AS p', 'p.id', '=', 'o.producto_id')
                ->select('p.imagen', 'p.nombre', 'p.descripcion', 'p.utiliza_imagen', 'o.precio', 'o.cantidad', 'o.nota')
                ->where('o.id', $request->ordenesid)
                ->get();

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', '');
                $p->descripcion = $p->descripcion;
            }

            return ['success' => 1, 'productos' => $producto];
        }else{
            return ['success' => 2];
        }
    }

    public function cancelarOrden(Request $request){
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($o = Ordenes::where('id', $request->ordenid)->first()){

            DB::beginTransaction();

            try {

                // el negocio puede cancelar la orden cuando quiera
                if($o->estado_7 == 0){

                    $fecha = Carbon::now('America/El_Salvador');

                    Ordenes::where('id', $request->ordenid)->update(['estado_7' => 1, 'visible_p' => 0,
                        'cancelado' => 2, 'fecha_7' => $fecha, 'mensaje_7' => $request->mensaje]);

                    DB::commit();

                    return ['success' => 1];

                }else{
                    return ['success' => 2]; // ya cancelada
                }
            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 3];
            }
        }else{
            return ['success' => 3]; // no encontrada
        }
    }


    public function borrarOrden(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            Ordenes::where('id', $request->ordenid)->update(['visible_p' => 0]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function procesarOrdenEstado2(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            // orden fue cancelada
            if($or->estado_7 == 1){
                return ['success' => 1];
            }

            if($or->estado_2 == 0){

                $fecha = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $request->ordenid)->update(['estado_2' => 1,
                    'fecha_2' => $fecha, 'visible_p' => 0, 'visible_p2' => 1, 'visible_p3' => 1]);

                // orden iniciada
                return ['success' => 2];
            }

            // orden iniciada
            return ['success'=> 2];
        }else{
            return ['success'=> 3];
        }
    }



    public function listadoPreparandoOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Afiliados::where('id', $request->id)->first()){

            // obtener comision

            $orden = Ordenes::where('estado_7', 0) // ordenes no canceladas
                ->where('visible_p2', 1) // estan en preparacion
                ->where('visible_p3', 1) // aun sin terminar de preparar
                ->where('estado_2', 1) // orden estado 4 preparacion
                ->get();

            foreach($orden as $o) {

                $infoCliente = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                $o->fecha_2 = date("h:i A d-m-Y", strtotime($o->fecha_2));
                $o->cliente = $infoCliente->nombre;

                $o->precio_consumido = number_format((float)$o->precio_consumido, 2, '.', '');
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionOrdenEnPreparacion(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $orden = Ordenes::where('id', $request->ordenid)->get();

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }


    public function finalizarOrden(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($o = Ordenes::where('id', $request->ordenid)->first()){

            $fechahoy = Carbon::now('America/El_Salvador');

            if($o->estado_3 == 0){
                Ordenes::where('id', $request->ordenid)->update(['visible_p2' => 0, 'visible_p3' => 0,
                    'estado_3' => 1, 'fecha_3' => $fechahoy]);
            }

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function listadoOrdenesCompletadasHoy(Request $request){

        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Afiliados::where('id', $request->id)->first()){

            $orden = Ordenes::where('estado_3', 1)
                ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
                ->get();

            foreach($orden as $o){

                $infoOrden = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $o->fecha_orden = date("h:i A ", strtotime($o->fecha_orden));
                $o->fecha_3 = date("h:i A ", strtotime($o->fecha_3));
                $o->fecha_6 = date("h:i A ", strtotime($o->fecha_6));

                $o->cliente = $infoOrden->nombre;

                $o->precio_consumido = number_format((float)$o->precio_consumido, 2, '.', ',');
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }

}