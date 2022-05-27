<?php

namespace App\Http\Controllers\Backend\Admin\Servicios;

use App\Http\Controllers\Controller;
use App\Models\BloqueServicios;
use App\Models\Categorias;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoriasController extends Controller
{
    public function indexBloque(){
        return view('backend.admin.bloques.vistabloques');
    }

    // tabla
    public function tablaBloque(){
        $bloques = BloqueServicios::orderBy('posicion')->get();

        return view('backend.admin.bloques.tablabloques', compact('bloques'));
    }

    public function nuevoBloque(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        $cadena = Str::random(15);
        $tiempo = microtime();
        $union = $cadena.$tiempo;
        $nombre = str_replace(' ', '_', $union);

        $extension = '.'.$request->imagen->getClientOriginalExtension();
        $nombreFoto = $nombre.strtolower($extension);
        $avatar = $request->file('imagen');
        $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

        if($upload){

            if($info = BloqueServicios::orderBy('posicion', 'DESC')->first()){
                $suma = $info->posicion + 1;
            }else{
                $suma = 1;
            }

            $ca = new BloqueServicios();
            $ca->nombre = $request->nombre;
            $ca->imagen = $nombreFoto;
            $ca->activo = 1;
            $ca->tiposervicio_id = 2;
            $ca->posicion = $suma;

            if($ca->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }

    public function informacionBloque(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($bloque = BloqueServicios::where('id', $request->id)->first()){

            return ['success' => 1, 'bloque' => $bloque];
        }else{
            return ['success' => 2];
        }
    }

    public function editarBloque(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = BloqueServicios::where('id', $request->id)->first()){

            if($request->hasFile('imagen')){

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.'.$request->imagen->getClientOriginalExtension();
                $nombreFoto = $nombre.strtolower($extension);
                $avatar = $request->file('imagen');
                $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

                if($upload){
                    $imagenOld = $info->imagen;

                    BloqueServicios::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'imagen' => $nombreFoto,
                        'activo' => $request->cbactivo
                    ]);

                    if(Storage::disk('imagenes')->exists($imagenOld)){
                        Storage::disk('imagenes')->delete($imagenOld);
                    }

                    return ['success' => 1];

                }else{
                    return ['success' => 2];
                }
            }else{
                // solo guardar datos

                BloqueServicios::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'activo' => $request->cbactivo
                ]);

                return ['success' => 1];
            }

        }else{
            return ['success' => 2];
        }
    }

    public function ordenarBloque(Request $request){

        $tasks = BloqueServicios::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }

    // *** CATEGORIAS *** //

    public function indexCategorias($id){

        return view('backend.admin.categorias.vistacategorias', compact('id'));
    }

    // tabla
    public function tablaCategorias($id){
        $categorias = Categorias::where('bloque_servicios_id', $id)->orderBy('posicion')->get();

        return view('backend.admin.categorias.tablacategorias', compact('categorias'));
    }

    public function nuevaCategorias(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = Categorias::orderBy('posicion', 'DESC')->first()){
            $suma = $info->posicion + 1;
        }else{
            $suma = 1;
        }

        $ca = new Categorias();
        $ca->bloque_servicios_id = $request->id;
        $ca->nombre = $request->nombre;
        $ca->activo = 1;
        $ca->visible = 1;
        $ca->posicion = $suma;

        if($ca->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }

    }

    public function informacionCategorias(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($categoria = Categorias::where('id', $request->id)->first()){

            return ['success' => 1, 'categoria' => $categoria];
        }else{
            return ['success' => 2];
        }
    }

    public function editarCategorias(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Categorias::where('id', $request->id)->first()){

            Categorias::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'activo' => $request->cbactivo,
                'visible' => $request->cbvisible
            ]);
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function ordenarCategorias(Request $request){

        $tasks = Categorias::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }


    // *** PRODUCTOS *** //

    public function indexProductos($id){

        $categoria = Categorias::where('id', $id)->pluck('nombre')->first();

        return view('backend.admin.productos.vistaproductos', compact('id', 'categoria'));
    }

    // tabla
    public function tablaProductos($id){
        $productos = Producto::where('categorias_id', $id)->orderBy('posicion')->get();

        foreach ($productos as $pp){
           $pp->precio = number_format((float)$pp->precio, 2, '.', ',');
        }

        return view('backend.admin.productos.tablaproductos', compact('productos'));
    }


    public function nuevoProducto(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($request->file('imagen')){

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen');
            $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

            if($upload){

                if($info = Producto::orderBy('posicion', 'DESC')->first()){
                    $suma = $info->posicion + 1;
                }else{
                    $suma = 1;
                }

                $ca = new Producto();
                $ca->categorias_id = $request->id;
                $ca->nombre = $request->nombre;
                $ca->imagen = $nombreFoto;
                $ca->descripcion = $request->descripcion;
                $ca->precio = $request->precio;
                $ca->disponibilidad = 1;
                $ca->activo = 1;
                $ca->posicion = $suma;
                $ca->utiliza_nota = $request->cbnota;
                $ca->nota = $request->nota;
                $ca->utiliza_imagen = $request->cbimagen;

                if($ca->save()){
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }
            }else{
                return ['success' => 2];
            }

        }else {

            $suma = Producto::sum('posicion');
            if($suma == null){
                $suma = 1;
            }else{
                $suma = $suma + 1;
            }

            $ca = new Producto();
            $ca->categorias_id = $request->id;
            $ca->nombre = $request->nombre;
            $ca->descripcion = $request->descripcion;
            $ca->precio = $request->precio;
            $ca->disponibilidad = 1;
            $ca->activo = 1;
            $ca->posicion = $suma;
            $ca->utiliza_nota = $request->cbnota;
            $ca->nota = $request->nota;
            $ca->utiliza_imagen = 0;

            if($ca->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


    public function informacionProductos(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = Producto::where('id', $request->id)->first()){

            return ['success' => 1, 'producto' => $info];
        }else{
            return ['success' => 2];
        }
    }

    public function editarProductos(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = Producto::where('id', $request->id)->first()){

            if($request->hasFile('imagen')){

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.'.$request->imagen->getClientOriginalExtension();
                $nombreFoto = $nombre.strtolower($extension);
                $avatar = $request->file('imagen');
                $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

                if($upload){
                    $imagenOld = $info->imagen;

                    Producto::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion,
                        'precio' => $request->precio,
                        'disponibilidad' => $request->cbdisponibilidad,
                        'activo' => $request->cbactivo,
                        'utiliza_nota' => $request->cbnota,
                        'nota' => $request->nota,
                        'utiliza_imagen' => $request->cbimagen,
                        'imagen' => $nombreFoto,
                    ]);

                    if(Storage::disk('imagenes')->exists($imagenOld)){
                        Storage::disk('imagenes')->delete($imagenOld);
                    }

                    return ['success' => 1];

                }else{
                    return ['success' => 2];
                }
            }else{
                // solo guardar datos

                if($info->imagen == null){
                    if($request->cbimagen == 1){
                        // quiere utilizar imagen pero no hay
                        return ['success' => 3];
                    }
                }

                Producto::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'precio' => $request->precio,
                    'disponibilidad' => $request->cbdisponibilidad,
                    'activo' => $request->cbactivo,
                    'utiliza_nota' => $request->cbnota,
                    'nota' => $request->nota,
                    'utiliza_imagen' => $request->cbimagen,
                ]);

                return ['success' => 1];
            }

        }else{
            return ['success' => 2];
        }
    }

    public function ordenarProductos(Request $request){

        $tasks = Producto::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }
}
