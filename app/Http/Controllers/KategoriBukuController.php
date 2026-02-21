<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriBuku;

class KategoriBukuController extends Controller
{
    public function index()
    {
        $kategori = KategoriBuku::orderBy('name')->get();
        return view('admin.kategori_buku.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate(['name'=>'required|unique:kategori_buku,name']);
        KategoriBuku::create($request->only('name'));
        return redirect()->back()->with('success','Kategori berhasil ditambahkan!');
    }

    public function destroy(KategoriBuku $kategori)
    {
        $kategori->delete();
        return redirect()->back()->with('success','Kategori berhasil dihapus!');
    }
}
