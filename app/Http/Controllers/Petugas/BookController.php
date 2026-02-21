<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\KategoriBuku;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('kategoris')->latest()->paginate(10);
        return view('petugas.books.index', compact('books'));
    }

    public function create()
    {
        $kategori = KategoriBuku::all();
        return view('petugas.books.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'penulis' => 'nullable|string',
            'penerbit' => 'nullable|string',
            'tahun' => 'nullable|integer',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'kategori' => 'nullable|array',
        ]);

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('uploads/covers', 'public');
        }

        $data['status'] = 'menunggu_konfirmasi';
        $book = Book::create($data);

        if ($request->kategori) {
            $book->kategoris()->sync($request->kategori);
        }

        return redirect()
            ->route('petugas.books.index')
            ->with('success', 'Buku dikirim dan menunggu persetujuan admin.');
    }

    public function edit(Book $book)
    {
        $kategori = KategoriBuku::all();
        return view('petugas.books.edit', compact('book', 'kategori'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'penulis' => 'nullable|string',
            'penerbit' => 'nullable|string',
            'tahun' => 'nullable|integer',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'kategori' => 'nullable|array',
        ]);

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('uploads/covers', 'public');
        }

        $data['status'] = 'menunggu_konfirmasi';
        $book->update($data);

        if ($request->kategori) {
            $book->kategoris()->sync($request->kategori);
        }

        return redirect()
            ->route('petugas.books.index')
            ->with('success', 'Perubahan dikirim, menunggu admin.');
    }

    public function destroy(Book $book)
    {
        $book->update(['status' => 'menunggu_hapus']);
        return back()->with('success', 'Permintaan hapus dikirim ke admin.');
    }
}
