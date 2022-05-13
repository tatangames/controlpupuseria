<?php

namespace App\Http\Controllers\Backend\Admin\Afiliado;

use App\Http\Controllers\Controller;
use App\Models\Afiliados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AfiliadoController extends Controller
{
    public function index(){
        return view('backend.admin.afiliados.vistaafiliados');
    }

    // tabla
    public function tablaAfiliados(){
        $afiliados = Afiliados::orderBy('nombre')->get();

        return view('backend.admin.afiliados.tablaafiliados', compact('afiliados'));
    }

    public function nuevo(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Afiliados::where('usuario', $request->usuario)->first()){
            return ['success' => 1];
        }


        $p = new Afiliados();
        $p->nombre = $request->nombre;
        $p->usuario = $request->usuario;
        $p->password = bcrypt($request->password);
        $p->token_fcm = null;
        $p->disponible = 0;
        $p->activo = 1;

        if($p->save()){
            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

    public function informacion(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = Afiliados::where('id', $request->id)->first()){

            return ['success' => 1, 'afiliado' => $p];
        }else{
            return ['success' => 2];
        }
    }

    public function editar(Request $request){
        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){ return ['success' => 0];}

        if(Afiliados::where('id', $request->id)->first()){

            if(Afiliados::where('usuario', $request->usuario)->where('id', '!=', $request->id)->first()){
                return [
                    'success' => 1
                ];
            }

            Afiliados::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'usuario' => $request->usuario,
                'activo' => $request->activo
            ]);

            // actualizar password
            if($request->passcheck == 1){
                Afiliados::where('id', $request->id)->update([
                    'password' => bcrypt('12345678')
                ]);
            }

            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }
}
