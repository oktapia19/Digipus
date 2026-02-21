<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/pengguna-dashboard2.css') }}">
</head>

<body>
<div class="header">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}">
    <h2>DIGIPUS</h2>
  </div>
  <div class="header-right">
    @include('components.notification-bell')
    @auth
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="header-action">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
      </form>
    @else
      <a href="{{ route('login') }}" class="header-action">
        <i class="fa-solid fa-right-to-bracket"></i> Login
      </a>
    @endauth
  </div>
</div>

<div class="hero">
  <div class="hero-text">
    @auth
      <h1>Selamat datang, {{ auth()->user()->name }}</h1>
      <p>di DIGIPUS</p>
    @else
      <h1>DIGIPUS</h1>
      <p>Membuka jendela dunia melalui literasi</p>
    @endauth
  </div>
</div>

<div class="section">
  <div class="fav-wrap">
    @if($terpopuler->isNotEmpty())
      <div class="fav-track" id="favTrack">
        @foreach($terpopuler as $book)
          <div class="fav-slide {{ $loop->first ? 'active' : '' }}">
            <img
              class="book-cover"
              src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://via.placeholder.com/300x430?text=Cover' }}"
              alt="{{ $book->judul }}"
            >
            <div>
              <div class="book-title">{{ $book->judul }}</div>
              <div class="book-author">{{ $book->penulis ?? 'Anonim' }}</div>
              <div class="book-desc">
                {{ $book->kategoris->pluck('name')->join(', ') ?: 'Kategori Umum' }}.
                Buku pilihan favorit pembaca aktif DIGIPUS.
              </div>
              <div class="meta-row">
                <span class="tag">{{ $book->kategoris->pluck('name')->join(', ') ?: 'Umum' }}</span>
                <span class="pinjam-count">{{ $book->total_pinjam }}x dipinjam</span>
              </div>
              <div class="meta-row">
                @auth
                  <a href="{{ route('books.show', $book->id) }}" class="book-action">Lihat Buku</a>
                @else
                  <a href="{{ route('login') }}" class="book-action">Login</a>
                @endauth
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="fav-controls">
        <div>
          <button class="arrow-btn" type="button" onclick="moveFav(-1)"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="arrow-btn" type="button" onclick="moveFav(1)"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="fav-dots" id="favDots"></div>
      </div>
    @else
      <div style="padding:18px">Tidak ada buku populer saat ini.</div>
    @endif
  </div>
</div>

<div class="about">
  <div class="about-top">
    <h3>Tentang DIGIPUS</h3>
    <p>DIGIPUS adalah platform perpustakaan digital untuk mempermudah peminjaman buku fisik secara cepat, rapi, dan transparan. Pengguna bisa memantau riwayat pinjam, status verifikasi, dan koleksi favorit dalam satu aplikasi.</p>
  </div>
  <div class="about-grid">
    <div class="about-item">
      <i class="fa-solid fa-bolt"></i>
      <h4>Proses Cepat</h4>
      <p>Ajukan peminjaman dalam hitungan detik dengan form yang sederhana.</p>
    </div>
    <div class="about-item">
      <i class="fa-solid fa-shield-halved"></i>
      <h4>Status Jelas</h4>
      <p>Setiap transaksi punya status dan bukti, sehingga lebih aman dan terpantau.</p>
    </div>
    <div class="about-item">
      <i class="fa-solid fa-heart"></i>
      <h4>Koleksi Pribadi</h4>
      <p>Simpan buku favoritmu dan temukan rekomendasi populer setiap minggu.</p>
    </div>
  </div>
</div>

<div class="bottom-nav">
  <a class="active">
    <i class="fa-solid fa-house"></i>
    Home
  </a>
  <a href="{{ route('buku.index') }}">
    <i class="fa fa-book"></i>buku
  </a>
  <a href="{{ route('peminjaman.index') }}">
    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat
  </a>
  <a href="{{ route('koleksi_buku.index') }}">
    <i class="fa-solid fa-heart"></i> Koleksi
  </a>
  <a href="{{ route('profile') }}">
    <i class="fa-solid fa-user"></i> Profil
  </a>
</div>
<script src="{{ asset('js/pengguna-dashboard2.js') }}"></script>
</body>
</html>


