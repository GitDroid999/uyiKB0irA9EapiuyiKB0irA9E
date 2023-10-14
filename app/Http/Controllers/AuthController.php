<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller{

    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if(!$user->api_token==null){
                return response()->json(['error' => 'Ya tienes un sesion iniciada!'], 401);
            }else{
                $token = $user->createToken('Login-Token')->plainTextToken;
                $user->api_token = $token;
                $user->save();
                    
                return response()->json(['token' => $token], 200);
            }
        }else{
            return response()->json(['error' => 'No autorizado'], 401);
        }
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users|regex:/^[a-zA-Z0-9._%+-]+@alumnos.santotomas\.cl$/',
            'password' => 'required|min:8',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }else {
            $user_register = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $user_register->save();
            $token = $user_register->createToken('Register-Token')->plainTextToken;

            return response()->json(['mensaje'=>'Cuenta creada con exito! - Verifica tu correo'], 200);
        }
    }

    public function logout(){
        // Obtener el token proporcionado en la solicitud
        $tokenR = request()->bearerToken();
        // Busca el usuario en la base de datos mediante el token
        $user = User::where('api_token', $tokenR)->first();
        //Verifica si el usuario existe
        if($user) {
            // Eliminar el token almacenado para cerrar la sesión
            $user->api_token = null;
            $user->save();
            return response()->json(['mensaje' => 'Sesión cerrada correctamente']);
        }
        // Si el usuario no existe, devolver un mensaje de error
        return response()->json(['mensaje' => 'No se pudo cerrar la sesión'], 400);
    }
}
