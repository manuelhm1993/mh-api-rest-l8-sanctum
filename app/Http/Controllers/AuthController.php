<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use \stdClass;

class AuthController extends Controller
{
    public function register(Request $request)
    {
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
}
