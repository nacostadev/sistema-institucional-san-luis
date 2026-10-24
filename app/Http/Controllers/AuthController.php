<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'clave' => ['required', 'string'],
        ]);

        $user = User::where('correo', $credentials['correo'])->where('estado', 'activo')->first();
        $passwordMatches = $user && (
            (str_starts_with($user->clave, '$2y$') && Hash::check($credentials['clave'], $user->clave)) ||
            hash_equals($user->clave, $credentials['clave'])
        );

        if (!$passwordMatches) {
            return back()->withErrors(['correo' => 'El correo o la contraseña no son correctos.'])->withInput($request->only('correo'));
        }

        Auth::login($user, $request->boolean('recordar'));
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
