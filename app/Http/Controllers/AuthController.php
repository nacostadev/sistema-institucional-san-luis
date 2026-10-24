<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\IdentidadService;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request, IdentidadService $identidad)
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'clave' => ['required', 'string'],
        ]);

        $correoHash = $identidad->hashCorreo($credentials['correo']);
        $user = User::where('correo_hash', $correoHash)->where('estado', 'activo')->first();
        $passwordMatches = $user && Hash::check($credentials['clave'], $user->password);

        if (!$passwordMatches) {
            return back()->withErrors(['correo' => 'El correo o la contraseña no son correctos.'])->withInput($request->only('correo'));
        }

        Auth::login($user);
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
