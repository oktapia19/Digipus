<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Koleksi Buku | DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/pengguna-koleksi-buku.css') }}">
</head>
<body>

<div class="header">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}">
    <h2>DIGIPUS</h2>
  </div>
  <div class="header-right">
    @include('components.notification-bell')
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="header-action">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </button>
    </form>
  </div>
</div>

<div class="page">
  <div class="hero">
    <h1>Koleksi Buku</h1>
    <p>Daftar koleksi buku yang kamu simpan</p>
  </div>

  <div class="books">
  @forelse($wishlist as $w)
    <div class="book">
      <form method="POST" action="{{ route('koleksi_buku.destroy', $w->book->id) }}">
        @csrf
        @method('DELETE')
        <button class="wishlist-badge" type="submit" title="Hapus dari favorit">
          <i class="fa-solid fa-heart"></i>
        </button>
      </form>
      <img src="{{ $w->book->cover ? asset('storage/'.$w->book->cover) : 'https://via.placeholder.com/200x300' }}">
      <h4>{{ $w->book->judul }}</h4>
      <span>{{ $w->book->penulis ?? '-' }}</span>

      <div class="tags">
        @forelse($w->book->kategoris as $kat)
          <span class="tag">{{ $kat->name }}</span>
        @empty
          <span class="tag">-</span>
        @endforelse
      </div>
      @php
        $sinopsis = $w->book->sinopsis ?? '-';
        if (preg_match('/Ã|â|�/', $sinopsis)) {
            $sinopsis = mb_convert_encoding($sinopsis, 'UTF-8', 'Windows-1252');
        }
      @endphp
      <div class="sinopsis">{{ $sinopsis }}</div>

      <div class="book-footer">
        <a href="{{ route('books.show', $w->book->id) }}">
          <button>Lihat</button>
        </a>
      </div>
    </div>
  @empty
    <div style="grid-column:1/-1;text-align:center;color:#777">
      Belum ada koleksi buku
    </div>
  @endforelse
  </div>
</div>

<div class="bottom-nav">
  <a href="{{ route('dashboard2') }}">
    <i class="fa-solid fa-house"></i>
    Home
  </a>
  <a href="{{ route('buku.index') }}">
    <i class="fa-solid fa-book"></i>
    Buku
  </a>
  <a href="{{ route('koleksi_buku.index') }}" class="active">
    <i class="fa-solid fa-heart"></i>
    Koleksi
  </a>
  <a href="{{ route('peminjaman.index') }}">
    <i class="fa-solid fa-clock-rotate-left"></i>
    Riwayat
  </a>
  <a href="{{ route('profile') }}">
    <i class="fa-solid fa-user"></i>
    Profil
  </a>
</div>

</body>
</html>











