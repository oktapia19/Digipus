<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Buku | DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/pengguna-buku.css') }}">
</head>

<body>
@php
  $isGuestView = ($guestMode ?? false) || !auth()->check();
@endphp

<!-- HEADER -->
<div class="header">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}">
    <h2>DIGIPUS</h2>
  </div>

  @if(!$isGuestView)
    <div class="header-right">
      @include('components.notification-bell')
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="header-action">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
      </form>
    </div>
  @else
    <a href="{{ route('login') }}" class="header-action">LOG IN</a>
  @endif
</div>

<!-- CARA MEMINJAM -->
<div class="cara-meminjam">
  <h2 class="cara-meminjam-title">Cara Meminjam</h2>
  <div class="steps">
    <div class="step">
      <div>1 Pilih buku</div>
      <div>Cari buku berdasarkan judul atau kategori yang tersedia</div>
    </div>
    <div class="step">
      <div>2 Klik Pinjam</div>
      <div>Tekan tombol pinjam pada buku yang kamu inginkan</div>
    </div>
    <div class="step">
      <div>3 Menunggu Konfirmasi</div>
      <div>Menunggu Konfirmasi dari Admin</div>
    </div>
    <div class="step">
      <div>4 Ambil Buku</div>
      <div>Buku siap dibaca sesuai jadwal peminjaman</div>
    </div>
  </div>
</div>

<!-- SEARCH + FAVORIT -->
<form class="search no-print" method="GET" action="{{ route('buku.index') }}">
  @if($isGuestView)
    <input type="hidden" name="guest" value="1">
  @endif
  <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari judul / penulis / penerbit...">
  <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
  @if(!$isGuestView)
    <a href="{{ route('koleksi_buku.index') }}" class="fav-link"><i class="fa-solid fa-heart"></i> Koleksi</a>
  @else
    <a href="{{ route('login') }}" class="fav-link"><i class="fa-solid fa-heart"></i> Koleksi</a>
  @endif
</form>

<!-- FILTER -->
<div class="filter no-print">
  <a href="{{ route('buku.index', array_merge(['q' => $q], $isGuestView ? ['guest' => 1] : [])) }}">
    <button class="{{ !$kategoriAktif ? 'active' : '' }}">Semua</button>
  </a>

  @foreach($kategori as $kat)
    <a href="{{ route('buku.index', array_merge(['kategori' => $kat->id, 'q' => $q], $isGuestView ? ['guest' => 1] : [])) }}">
      <button class="{{ $kategoriAktif == $kat->id ? 'active' : '' }}">{{ $kat->name }}</button>
    </a>
  @endforeach
</div>

<!-- LIST BUKU -->
<div class="books print-area">
  <div class="print-header" style="grid-column:1/-1;">
    <h1>Laporan Buku DIGIPUS</h1>
    <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
  </div>
@forelse($books as $book)
  <div class="book">
    <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://via.placeholder.com/200x300' }}">
    <h4>{{ $book->judul }}</h4>
    <span>{{ $book->penulis ?? '-' }}</span>

    <div class="tags">
      @forelse($book->kategoris as $kat)
        <span class="tag">{{ $kat->name }}</span>
      @empty
        <span class="tag">-</span>
      @endforelse
    </div>
    @php
      $reviewCount = (int) ($book->reviews_count ?? 0);
      $avgRating = $reviewCount > 0 ? round((float) ($book->reviews_avg_rating ?? 0), 1) : 0;
      $filledStars = (int) round($avgRating);
    @endphp

    <div class="book-footer">
      <div class="rating-line">
        <div class="book-rating" title="{{ $reviewCount }} ulasan">
          @for($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= $filledStars ? 'filled' : 'empty' }}">{!! $i <= $filledStars ? '&#9733;' : '&#9734;' !!}</span>
          @endfor
          <span class="rating-value">{{ number_format($avgRating, 1) }}</span>
        </div>
        <div class="borrow-count">{{ (int) ($book->total_pinjam ?? 0) }}x dipinjam</div>
      </div>
      <div style="display:flex;gap:6px;align-items:center">
        @if(!$isGuestView)
          @if(in_array($book->id, $wishlistBookIds))
            <form method="POST" action="{{ route('koleksi_buku.destroy', $book->id) }}">
              @csrf
              @method('DELETE')
              <button class="wishlist-btn active" type="submit" title="Hapus dari favorit"><i class="fa-solid fa-heart"></i></button>
            </form>
          @else
            <form method="POST" action="{{ route('koleksi_buku.store', $book->id) }}">
              @csrf
              <button class="wishlist-btn" type="submit" title="Simpan ke favorit"><i class="fa-regular fa-heart"></i></button>
            </form>
          @endif
        @else
          <a href="{{ route('login') }}" class="wishlist-btn" title="Login untuk favorit">
            <i class="fa-regular fa-heart"></i>
          </a>
        @endif
        <a href="{{ route('books.show', array_merge(['book' => $book->id], $isGuestView ? ['guest' => 1] : [])) }}">
          <button>Lihat</button>
        </a>
      </div>
    </div>
  </div>
@empty
  <div style="grid-column:1/-1;text-align:center;color:#777">
    😔 Belum ada buku
  </div>
@endforelse
</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
  @if(!$isGuestView)
    <a href="{{ route('dashboard2') }}">
      <i class="fa-solid fa-house"></i>
      Home
    </a>

    <a class="active">
      <i class="fa-solid fa-book"></i>
      Buku
    </a>

    <a href="{{ route('peminjaman.index') }}">
      <i class="fa-solid fa-clock-rotate-left"></i>
      Riwayat
    </a>

    <a href="{{ route('koleksi_buku.index') }}">
      <i class="fa-solid fa-heart"></i>
      Koleksi
    </a>

    <a href="{{ route('profile') }}">
      <i class="fa-solid fa-user"></i>
      Profil
    </a>
  @else
    <a href="{{ route('home') }}">
      <i class="fa-solid fa-house"></i>
      Home
    </a>

    <a class="active">
      <i class="fa-solid fa-book"></i>
      Buku
    </a>
  @endif
</div>

</body>
</html>






