<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==================== MENAMPILKAN FORM LOGIN ====================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ==================== MEMPROSES LOGIN ====================
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan Role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.peminjaman.index');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.katalog');
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // ==================== MENAMPILKAN FORM REGISTRASI ====================
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // ==================== MEMPROSES REGISTRASI ====================
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:6', 'confirmed'], // Wajib ada input password_confirmation di form
        ]);

        $user = User::create([
            'name'     => $validatedData['name'],
            'email'    => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role'     => 'peminjam', // Default user baru sebagai peminjam
        ]);

        // Otomatis login setelah registrasi berhasil
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('peminjam.katalog')->with('success', 'Registrasi berhasil!');
    }

    // ==================== PROSES LOGOUT ====================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}