<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use \stdClass;

class AuthController extends Controller
{
    public function register(Request $request) {
        // Crear reglas de validación manualmente
        $validator = Validator::make(
            // Datos de entrada
            $request->all(),
            // Reglas de validación
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]
        );

        // Si la validación falla devolver los errores
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        // Si la validación tiene éxito, crear un nuevo usuario
        $validated = $validator->validated();

        // Encriptar la password
        $validated['password'] = Hash::make($validated['password']);

        // Crear un nuevo usuario en la BD
        $user = User::create($validated);

        // Crear un token de autenticación que se devuelve en la petición
        $token = $user->createToken('auth_token')->plainTextToken;

        // Respuesta json
        return response()->json([
            'data'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer', // Tipo de token requerido por sanctum
        ]);
    }

    public function login(Request $request) {
        $data   = [];
        $status = 200;

        // Si la autenticación falla
        if(!Auth::attempt($request->only('email', 'password'))) {
            $data   = ['Error' => 'No autorizado'];
            $status = 401;
        }
        // Si la autenticación tiene éxito
        else {
            try {
                $user  = User::where('email', $request->email)->firstOrFail();
                $token = $user->createToken('auth_token')->plainTextToken;

                $data   = [
                    'message'     => "Hola {$user->name}",
                    'accessToken' => $token,
                    'tokenType'   => 'Bearer',
                    'user'        => $user,
                ];
            }
            catch (ModelNotFoundException $e) {
                $data   = ['Error' => $e->getMessage()];
                $status = 404;
            }

        }

        return response()->json($data, $status);
    }

    public function logout(Request $request) {
        $data   = [];
        $status = 200;

        try {
            // Eliminar los tokens del usuario y cerrar la sesión (tokens) es provisto por el Trait HasApiTokens
            // auth()->user()->tokens()->delete();
            $request->user()->tokens()->delete();

            // Eliminar el token actual de la sesión
            // $request->user()->currentAccessToken()->delete();

            $data   = ['message' => 'Has finalizado sesión exitosamente y sus tokens fueron borrados'];
        }
        catch (\Exception $e) {
            $data   = ['error' => $e->getMessage()];
            $status = 400;
        }

        return response()->json($data, $status);
    }
}
