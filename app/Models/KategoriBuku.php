<?php
// app/Models/KategoriBuku.php - BUAT/TAMBAH INI
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBuku extends Model
{
    protected $table = 'kategori_buku'; // Pastikan nama tabel bener
    
    protected $fillable = ['name']; // Pastikan ada kolom ini
    
    public function books()
    {
        return $this->belongsToMany(Book::class, 'kategori_relasi', 
            'kategori_buku_id', 'book_id');
    }
}
