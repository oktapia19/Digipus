<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $borrowedCount = Peminjaman::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'waiting_return'])
            ->count();
        $returnedCount = Peminjaman::where('user_id', $user->id)
            ->where('status', 'returned')
            ->count();

        return view('user.profile', compact('user', 'borrowedCount', 'returnedCount'));
    }

    public function updatePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $user = Auth::user();

            // hapus foto lama
            if ($user->photo) {
                Storage::disk('public')->delete('profile/' . $user->photo);
            }

            if ($request->hasFile('photo')) {
                $filename = time() . '_' . $user->id . '.' . $request->file('photo')->getClientOriginalExtension();
                $request->file('photo')->storeAs('profile', $filename, 'public');

                $user->update([
                    'photo' => $filename
                ]);

                return back()->with('success', 'Foto berhasil diupdate');
            } else {
                return back()->with('error', 'File tidak ditemukan');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'alamat' => 'nullable|string|max:1000',
            'no_telepon' => 'nullable|string|max:30',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'alamat' => $data['alamat'] ?? null,
            'no_telepon' => $data['no_telepon'] ?? null,
        ]);

        if (!empty($data['password'])) {
            if (!Hash::check($data['current_password'] ?? '', $user->password)) {
                return back()->with('error', 'Password lama salah');
            }

            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama salah');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui');
    }
}
