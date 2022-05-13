<?php

namespace App\Http\Controllers\Backend\Admin\Eventos;

use App\Http\Controllers\Controller;
use App\Models\BloquesEventos;
use App\Models\EventoImagenes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventosController extends Controller
{
    public function indexEventos(){
        return view('backend.admin.eventos.vistabloqueeventos');
    }

    // tabla
    public function tablaEventos(){
        $eventos = BloquesEventos::orderBy('posicion')->get();

        foreach ($eventos as $ee){
            $ee->fecha = date("d-m-Y", strtotime($ee->fecha));
        }

        return view('backend.admin.eventos.tablabloqueeventos', compact('eventos'));
    }

    public function nuevoEvento(Request $request){

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

            $suma = BloquesEventos::sum('posicion');
            if($suma == null){
                $suma = 1;
            }else{
                $suma = $suma + 1;
            }

            $fecha = Carbon::now('America/El_Salvador');

            $ca = new BloquesEventos();
            $ca->nombre = $request->nombre;
            $ca->imagen = $nombreFoto;
            $ca->activo = 1;
            $ca->fecha = $fecha;
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

    public function informacionEvento(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($evento = BloquesEventos::where('id', $request->id)->first()){

            return ['success' => 1, 'evento' => $evento];
        }else{
            return ['success' => 2];
        }
    }

    public function editarEvento(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = BloquesEventos::where('id', $request->id)->first()){

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

                    BloquesEventos::where('id', $request->id)->update([
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

                BloquesEventos::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'activo' => $request->cbactivo
                ]);

                return ['success' => 1];
            }

        }else{
            return ['success' => 2];
        }
    }

    public function ordenarEvento(Request $request){

        $tasks = BloquesEventos::all();

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

    public function borrarEvento(Request $request){
        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(BloquesEventos::where('id', $request->id)->first()){

            // borrar imagenes
            $eve = EventoImagenes::where('evento_id', $request->id)->get();

            foreach ($eve as $ee){
                if(Storage::disk('imagenes')->exists($ee->imagen)){
                    Storage::disk('imagenes')->delete($ee->imagen);
                }
            }

            EventoImagenes::where('evento_id', $request->id)->delete();
            BloquesEventos::where('id', $request->id)->delete();
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // **** IMAGENES *****


    public function indexImagenes($id){
        return view('backend.admin.eventos.imagenes.vistaimagenes', compact('id'));
    }

    // tabla
    public function tablaImagenes($id){
        $eventos = EventoImagenes::where('evento_id', $id)->orderBy('posicion')->get();

        return view('backend.admin.eventos.imagenes.tablaimagenes', compact('eventos'));
    }


    public function nuevoEventoImagen(Request $request){

        $regla = array(
            'id' => 'required'
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

            $suma = EventoImagenes::sum('posicion');
            if($suma == null){
                $suma = 1;
            }else{
                $suma = $suma + 1;
            }

            $ca = new EventoImagenes();
            $ca->evento_id = $request->id;
            $ca->imagen = $nombreFoto;
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

    public function borrarEventoImagen(Request $request){
        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = EventoImagenes::where('id', $request->id)->first()){

            // borrar imagenes
            if(Storage::disk('imagenes')->exists($info->imagen)){
                Storage::disk('imagenes')->delete($info->imagen);
            }

            EventoImagenes::where('id', $request->id)->delete();
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function ordenarEventoImagen(Request $request){

        $tasks = BloquesEventos::all();

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
