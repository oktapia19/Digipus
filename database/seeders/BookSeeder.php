<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        Book::create([
            'judul' => 'Matematika Seru untuk SMP',
            'penulis' => 'Budi Santoso',
            'penerbit' => 'EduMedia',
            'tahun' => 2022,
            'sinopsis' => 'Buku ini membahas konsep dasar matematika SMP dengan latihan interaktif.',
            'cover' => 'images/matematika-seru.jpg',
            'stok' => 3,
        ]);

        Book::create([
            'judul' => 'Fisika Menyenangkan',
            'penulis' => 'Rina Wijaya',
            'penerbit' => 'SainsBook',
            'tahun' => 2021,
            'sinopsis' => 'Fisika jadi mudah dipahami dengan ilustrasi dan contoh nyata.',
            'cover' => 'images/covers/fisika-menyenangkan.jpg',
            'stok' => 5,
        ]);

        Book::create([
            'judul' => 'Negeri Senja',
            'penulis' => 'Andi Pratama',
            'penerbit' => 'LiterasiKu',
            'tahun' => 2020,
            'sinopsis' => 'Cerita fiksi yang seru dan mendidik, cocok untuk remaja.',
            'cover' => 'images/covers/negri-senja.jpg',
            'stok' => 2,
        ]);

        // Tambah buku lain sesuai kebutuhan
    }
}
