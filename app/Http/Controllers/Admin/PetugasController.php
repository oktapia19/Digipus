<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetugasController extends Controller
{
    public function dashboard()
    {
        return view('petugas.dashboard');
    }

    public function books(Request $request)
    {
        $books = Book::with('kategoris')->paginate(10);
        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:255',
            'penulis' => 'required',
            'penerbit' => 'required',
            'tahun' => 'required|numeric|min:1900|max:'.date('Y'),
            'stok' => 'required|numeric|min:0',
            'sinopsis' => 'required',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategoris' => 'array',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book = Book::create($data);

        if ($request->kategoris) {
            $book->kategoris()->attach($request->kategoris);
        }

        return redirect()->route('petugas.books.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(Book $book)
    {
        $book->load('kategoris');
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $book->load('kategoris');
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'judul' => 'required|max:255',
            'penulis' => 'required',
            'penerbit' => 'required',
            'tahun' => 'required|numeric|min:1900|max:'.date('Y'),
            'stok' => 'required|numeric|min:0',
            'sinopsis' => 'required',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategoris' => 'array',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);

        if ($request->kategoris) {
            $book->kategoris()->sync($request->kategoris);
        }

        return redirect()->route('petugas.books.index')
            ->with('success', 'Buku berhasil diupdate!');
    }

    public function destroy(Book $book)
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }
        $book->delete();
        
        return redirect()->route('petugas.books.index')
            ->with('success', 'Buku berhasil dihapus!');
    }
}
