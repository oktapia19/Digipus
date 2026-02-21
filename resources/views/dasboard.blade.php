<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/beranda.css') }}">
</head>

<body>

<!-- HEADER -->
<div class="header">
  <img src="{{ asset('images/logo digipus.png') }}">
  <h2>DIGIPUS</h2>

  <a href="{{ route('login') }}" style="margin-left:auto;color:#6B33B8;font-weight:600;text-decoration:none">
    <i class="fa-solid fa-right-to-bracket"></i> Login
  </a>
</div>

<!-- HERO -->
<div class="hero">
  <div class="hero-content">
    <h1>Perpustakaan Digital</h1>
    <p>DIGIPUS mempermudah peminjaman buku offline dengan sistem digital yang cepat dan rapi.</p>
    <div class="hero-actions">
      <a href="{{ route('login') }}" class="btn-primary">Daftar DIGIPUS</a>
      <a href="{{ route('buku.index', ['guest' => 1]) }}" class="btn-outline">Jelajahi Buku</a>
    </div>
  </div>
</div>

<!-- APA ITU DIGIPUS -->
<section class="section">
  <div class="about-box">
    <div class="about-text">
      <h3>Apa itu DIGIPUS?</h3>
      <p><b>DIGIPUS</b> adalah aplikasi perpustakaan digital untuk mempermudah peminjaman buku <b>offline</b> secara modern.</p>
      <p>Tanpa ribet, tanpa catatan manual, semua tercatat otomatis.</p>
      <div class="about-points">
        <span class="point">📚 Cari Buku</span>
        <span class="point">📝 Pinjam Offline</span>
        <span class="point">⚡ Cepat</span>
        <span class="point">🔒 Aman</span>
      </div>
    </div>
    <div class="about-visual">
      <i class="fa-solid fa-building-columns"></i>
      <h4>Solusi Perpustakaan Modern</h4>
      <p>Cocok untuk sekolah & perpustakaan umum.</p>
    </div>
  </div>
</section>

<!-- WHY -->
<section class="section">
  <h3 class="section-title">Kenapa Harus DIGIPUS?</h3>
  <div class="why">
    <div class="why-card">
      <i class="fa-solid fa-clock"></i>
      <h4>Hemat Waktu</h4>
      <p>Proses cepat & efisien.</p>
    </div>
    <div class="why-card">
      <i class="fa-solid fa-book"></i>
      <h4>Buku Lengkap</h4>
      <p>Koleksi favorit & populer.</p>
    </div>
    <div class="why-card">
      <i class="fa-solid fa-user-check"></i>
      <h4>Aman & Rapi</h4>
      <p>Data tercatat otomatis.</p>
    </div>
  </div>
</section>

<!-- BUKU TERPOPULER -->
<section class="section">
  <h3 class="section-title">Buku Terpopuler</h3>
  <div class="books">
    @foreach($terpopuler as $book)
      <div class="book-card">
        <div class="book-cover">
          <img src="{{ $book->cover 
      ? asset('storage/'.$book->cover) 
      : 'https://via.placeholder.com/200x300?text=Cover' }}">
        </div>
        <div class="book-info">
          <h4>{{ $book->judul }}</h4>
          <span>{{ $book->penulis ?? 'Anonim' }}</span>
        </div>
      </div>
    @endforeach
  </div>
</section>

<!-- NAVBAR -->
<div class="navbar">
  <a class="active"><i class="fa fa-circle-info"></i>Tentang</a>
  <a href="{{ route('buku.index', ['guest' => 1]) }}"><i class="fa fa-book"></i>Buku</a>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div>
    <h4>DIGIPUS</h4>
    <div class="line"><i class="fa-solid fa-location-dot"></i> Jl. Pendidikan No. 12, Jakarta</div>
    <div class="line"><i class="fa-solid fa-envelope"></i> support@digipus.id</div>
    <div class="badge">&copy; {{ date('Y') }} DIGIPUS</div>
  </div>
  <div>
    <h4>Kontak</h4>
    <div class="line"><i class="fa-solid fa-phone"></i> 0812-3456-7890</div>
    <div class="line"><i class="fa-brands fa-instagram"></i> <a href="https://www.instagram.com/digipus.id?igsh=MTV1YjVjYWI3eW9wOA==" target="_blank" rel="noopener">digipus.id</a></div>
  </div>
  <div>
    <h4>Tautan</h4>
    <div class="line"><i class="fa-solid fa-book"></i> <a href="{{ route('buku.index', ['guest' => 1]) }}">Jelajahi Buku</a></div>
    <div class="line"><i class="fa-solid fa-right-to-bracket"></i> <a href="{{ route('login') }}">Login</a></div>
  </div>
</footer>

</body>
</html>




