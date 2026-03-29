<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_perfil' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'foto_perfil.required' => 'Debe seleccionar una imagen.',
            'foto_perfil.image'    => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes'    => 'La imagen debe ser JPEG, PNG, JPG o GIF.',
            'foto_perfil.max'      => 'La imagen no puede superar los 2MB.',
        ]);

        $usuario = Auth::user();

        // Eliminar foto anterior si existe
        if ($usuario->foto_perfil && Storage::disk('public')->exists('fotos_perfil/' . $usuario->foto_perfil)) {
            Storage::disk('public')->delete('fotos_perfil/' . $usuario->foto_perfil);
        }

        // Guardar nueva foto
        $file = $request->file('foto_perfil');
        $filename = 'user_' . $usuario->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('fotos_perfil', $filename, 'public');

        $usuario->foto_perfil = $filename;
        $usuario->save();

        return back()->with('success', 'Foto de perfil actualizada correctamente.');
    }

    public function eliminarFoto()
    {
        $usuario = Auth::user();

        // Eliminar foto si existe
        if ($usuario->foto_perfil && Storage::disk('public')->exists('fotos_perfil/' . $usuario->foto_perfil)) {
            Storage::disk('public')->delete('fotos_perfil/' . $usuario->foto_perfil);
        }

        $usuario->foto_perfil = null;
        $usuario->save();

        return back()->with('success', 'Foto de perfil eliminada correctamente.');
    }
}
