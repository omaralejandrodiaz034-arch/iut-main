<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('perfil.show', ['usuario' => Auth::user()]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_actual'      => ['required', 'string'],
            'password'             => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation'=> ['required', 'string'],
        ], [
            'password_actual.required'  => 'Debe ingresar su contraseña actual.',
            'password.required'         => 'La nueva contraseña es requerida.',
            'password.min'              => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        $usuario = Auth::user();

        if (! Hash::check($request->password_actual, $usuario->hash_password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.'])->withInput();
        }

        $usuario->hash_password = Hash::make($request->password);
        $usuario->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function updateProfile(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'correo'   => ['required', 'email', 'max:200', 'unique:usuarios,correo,'.$usuario->id],
        ], [
            'nombre.required'   => 'El nombre es requerido.',
            'apellido.required' => 'El apellido es requerido.',
            'correo.required'   => 'El correo es requerido.',
            'correo.unique'     => 'Este correo ya está en uso.',
        ]);

        $usuario->nombre   = $request->nombre;
        $usuario->apellido = $request->apellido;
        $usuario->correo   = $request->correo;
        $usuario->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
