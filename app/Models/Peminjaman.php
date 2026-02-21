<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id', 'book_id', 'durasi', 'durasi_satuan', 'tanggal_kembali', 'tanggal_pinjam', 'tanggal_pengembalian', 'kondisi_buku', 'denda_tambahan', 'status', 'kode', 'receipt_path', 'alamat', 'no_telepon'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
