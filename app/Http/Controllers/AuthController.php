<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Book;
use App\Models\Role;

class AuthController extends Controller
{
    /* =====================================================
    | LOGIN (SATU PINTU SEMUA ROLE) — FIX
    ===================================================== */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // ================= ADMIN =================
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        // ================= PETUGAS =================
        if (Auth::guard('petugas')->attempt($credentials)) {
            return redirect()->route('petugas.dashboard');
        }

        // ================= USER =================
        if (Auth::guard('web')->attempt($credentials)) {
            return redirect()->route('dashboard2');
        }

        return redirect()
            ->route('login')
            ->with('login_error', 'Username atau kata sandi salah')
            ->withInput($request->only('email'));
    }

    /* =====================================================
    | REGISTER USER (WEB)
    ===================================================== */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $payload = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        if (Schema::hasTable('roles') && Schema::hasColumn('users', 'role_id')) {
            $payload['role_id'] = Role::firstOrCreate(['name' => 'user'])->id;
        }

        User::create($payload);

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dibuat, silakan login');
    }

    /* =====================================================
    | LOGOUT (SEMUA ROLE) — BIARIN
    ===================================================== */
    public function logout(Request $request)
    {
        // Logout guard sesuai konteks halaman saat ini agar tidak menendang role lain.
        if ($request->is('admin/*') && Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif ($request->is('petugas/*') && Auth::guard('petugas')->check()) {
            Auth::guard('petugas')->logout();
        } elseif (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('petugas')->check()) {
            Auth::guard('petugas')->logout();
        }

        // Hanya invalidate jika semua guard sudah tidak login.
        if (!Auth::guard('web')->check() && !Auth::guard('admin')->check() && !Auth::guard('petugas')->check()) {
            $request->session()->invalidate();
        } else {
            $request->session()->regenerate();
        }
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /* =====================================================
    | DASHBOARD USER
    ===================================================== */
    public function dashboard2()
    {
        $user = Auth::guard('web')->user();

        $terpopuler = Book::with('kategoris')
            ->where('status', 'disetujui')
            ->withCount(['peminjamans as total_pinjam' => function ($q) {
                $q->whereIn('status', ['confirmed', 'waiting_return', 'returned']);
            }])
            ->orderByDesc('total_pinjam')
            ->orderBy('judul')
            ->take(3)
            ->get();

        return view('user.dashboard2', compact('user', 'terpopuler'));
    }

    /* =====================================================
    | DASHBOARD PETUGAS
    ===================================================== */
    public function dashboardPetugas()
    {
        $petugas = Auth::guard('petugas')->user();
        $buku = Book::latest()->take(5)->get();

        return view('petugas.dashboard', compact('petugas', 'buku'));
    }
}
