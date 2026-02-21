<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;

class BookController extends Controller
{
    /* ================= HOME / LANDING ================= */
    public function home()
    {
        $terpopuler = Book::with('kategoris')
            ->where('status', 'disetujui')
            ->withCount(['peminjamans as total_pinjam' => function ($q) {
                $q->whereIn('status', ['confirmed', 'waiting_return', 'returned']);
            }])
            ->orderByDesc('total_pinjam')
            ->orderBy('judul')
            ->take(3)
            ->get();

        return view('dasboard', compact('terpopuler'));
    }

    /* ================= DETAIL BUKU ================= */
    public function show(Request $request, Book $book)
    {
        if ($book->status !== 'disetujui') {
            abort(404);
        }

        $guestMode = $request->boolean('guest');
        $book->load(['kategoris', 'reviews.user']);

        $ratingCount = $book->reviews->count();
        $avgRating = $ratingCount ? round($book->reviews->avg('rating'), 1) : 0;
        $isWishlisted = (!$guestMode && auth()->check())
            ? $book->wishlists()->where('user_id', auth()->id())->exists()
            : false;
        $activeBorrowCount = (!$guestMode && auth()->check())
            ? Peminjaman::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'confirmed', 'waiting_return'])
                ->count()
            : 0;
        $canBorrow = $activeBorrowCount < 2;

        return view('books.show', compact('book', 'avgRating', 'ratingCount', 'isWishlisted', 'activeBorrowCount', 'canBorrow', 'guestMode'));
    }

    /* ================= PINJAM BUKU ================= */
    public function borrow(Book $book)
    {
        if ($book->status !== 'disetujui') {
            return back()->with('error', 'Buku belum tersedia');
        }

        if ($book->stok < 1) {
            return back()->with('error', 'Stok buku habis');
        }

        $book->decrement('stok');

        return back()->with(
            'success',
            "Berhasil meminjam buku: {$book->judul}"
        );
    }

    /* ================= DASHBOARD USER ================= */
    public function dashboard2()
    {
        $terpopuler = Book::with('kategoris')
            ->where('status', 'disetujui')
            ->withCount(['peminjamans as total_pinjam' => function ($q) {
                $q->whereIn('status', ['confirmed', 'waiting_return', 'returned']);
            }])
            ->orderByDesc('total_pinjam')
            ->orderBy('judul')
            ->take(3)
            ->get();

        return view('user.dashboard2', compact('terpopuler'));
    }

    /* ================= LIST BUKU USER ================= */
    public function userBuku(Request $request)
    {
        $guestMode = $request->boolean('guest');
        $kategoriAktif = $request->query('kategori');
        $q = trim((string) $request->query('q', ''));
        $kategori = KategoriBuku::all();

        $books = Book::with('kategoris')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount([
                'peminjamans as total_pinjam' => function ($q) {
                    $q->whereIn('status', ['confirmed', 'waiting_return', 'returned']);
                }
            ])
            ->where('status', 'disetujui')
            ->when($kategoriAktif, function ($q) use ($kategoriAktif) {
                $q->whereHas('kategoris', function ($k) use ($kategoriAktif) {
                    $k->where('kategori_buku_id', $kategoriAktif);
                });
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('judul', 'like', "%{$q}%")
                        ->orWhere('penulis', 'like', "%{$q}%")
                        ->orWhere('penerbit', 'like', "%{$q}%")
                        ->orWhere('isbn', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->get();

        $wishlistBookIds = (!$guestMode && auth()->check())
            ? auth()->user()->wishlists()->pluck('book_id')->toArray()
            : [];

        return view('user.buku', compact(
            'books',
            'kategori',
            'kategoriAktif',
            'q',
            'guestMode',
            'wishlistBookIds'
        ));
    }
}
