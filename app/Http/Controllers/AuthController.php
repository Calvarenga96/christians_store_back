<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        /**
         * Se genera un nuevo usuario y se guarda en la base de datos
         */
        $user           = new User;
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json($user, 201);
    }

    public function login(LoginRequest $request)
    {
        /**
         * Se toman los datos enviados del front
         */
        $credentials = $request->only('email', 'password');

        /**
         * Se compara con la base de datos si los datos coinciden
         */
        if (Auth::attempt($credentials)) {
            /**
             * Si el usuario se autentica correctamente, se solicita la instancia
             * del usuario autenticado
             */
            $user                   = Auth::user();
            $token                  = $this->generateToken($user->id);
            $nowTo30DaysInMinutes   = 60 * 24 * 30;

            $cookie = cookie(
                'tokenStore',
                $token,
                $nowTo30DaysInMinutes
            );

            return response()->json([
                'message'   => 'Credenciales correctas',
                'user'      => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'token' => $token,
                ],
            ], 200)->cookie($cookie);
        }

        return response()->json([
            'message' => 'Credenciales inválidas'
        ], 401);
    }

    public function logout(LogoutRequest $request)
    {
        $user           = User::find($request->id);
        $user->token    = null;
        $user->save();

        return response()->json();
    }

    private function generateToken($id)
    {
        $user           = User::find($id);
        $user->token    = hash('sha256', Str::random(60));
        $user->save();

        return $user->token;
    }
}
