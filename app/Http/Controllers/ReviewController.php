<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|integer|exists:peminjamans,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $peminjaman = Peminjaman::with('book')
            ->where('id', $request->input('peminjaman_id'))
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($peminjaman->status !== 'returned') {
            return redirect()->back()->withErrors(['rating' => 'Ulasan hanya bisa dikirim setelah buku dikembalikan.']);
        }

        $already = Review::where('peminjaman_id', $peminjaman->id)->exists();
        if ($already) {
            return redirect()->back()->withErrors(['rating' => 'Ulasan untuk peminjaman ini sudah ada.']);
        }

        Review::create([
            'user_id' => $user->id,
            'book_id' => $peminjaman->book_id,
            'peminjaman_id' => $peminjaman->id,
            'rating' => (int) $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->back()->with('success', 'Ulasan berhasil dikirim.');
    }
}
