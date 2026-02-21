<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $wishlist = $user->wishlists()->with('book.kategoris')->latest()->get();

        return view('user.koleksi_buku', compact('wishlist'));
    }

    public function store(Request $request, Book $book)
    {
        $user = $request->user();

        Wishlist::firstOrCreate([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        return redirect()->back()->with('success', 'Buku disimpan ke koleksi.');
    }

    public function destroy(Request $request, Book $book)
    {
        $user = $request->user();

        Wishlist::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->delete();

        return redirect()->back()->with('success', 'Buku dihapus dari koleksi.');
    }
}
