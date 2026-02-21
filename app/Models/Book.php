<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Peminjaman;
use App\Models\Wishlist;
use App\Models\Review;

class Book extends Model
{
    protected $fillable = [
        'judul',
        'penulis',
        'penerbit',
        'tahun',
        'sinopsis',
        'cover',
        'stok',
        'isbn',
        'status',
    ];

    public function kategoris()
    {
        return $this->belongsToMany(
            KategoriBuku::class,
            'kategori_relasi',
            'book_id',
            'kategori_buku_id'
        )->withTimestamps();
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
