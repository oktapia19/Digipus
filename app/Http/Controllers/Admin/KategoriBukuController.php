<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriBuku;
use Illuminate\Http\Request;

class KategoriBukuController extends Controller
{
    public function index()
    {
        $kategoris = KategoriBuku::withCount('books')->paginate(10);
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:kategori_buku,name',
        ]);

        KategoriBuku::create($request->only('name'));

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(KategoriBuku $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriBuku $kategori)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:kategori_buku,name,' . $kategori->id,
        ]);

        $kategori->update($request->only('name'));

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(KategoriBuku $kategori)
    {
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
