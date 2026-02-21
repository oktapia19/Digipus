<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Buku | DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/buku-detail.css') }}">
</head>

<body>
@php
  $isGuestView = ($guestMode ?? false) || !auth()->check();
@endphp

<!-- HEADER -->
<div class="header">
  <div></div>
  <a href="{{ route('buku.index', $isGuestView ? ['guest' => 1] : []) }}" class="back">Kembali</a>



</div>

<!-- DETAIL BUKU -->
<div class="card">
  <div class="layout">

<img 
  class="cover"
  src="{{ $book->cover 
    ? asset('storage/'.$book->cover) 
    : 'https://via.placeholder.com/200x300?text=Cover' 
  }}"
>



    <div class="info">
      <h1>{{ $book->judul }}</h1>
      <div class="stok">Stok tersedia: {{ $book->stok }} buku</div>
      @php
        $roundedRating = (int) round($avgRating);
      @endphp
      <div class="rating">
        @for($i = 1; $i <= 5; $i++)
          @if($i <= $roundedRating)
            ★
          @else
            <span>★</span>
          @endif
        @endfor
        <span class="rating-count">({{ $ratingCount }} ulasan)</span>
      </div>
      <div class="meta">
        <div><b>Penulis:</b> {{ $book->penulis }}</div>
        <div><b>Penerbit:</b> {{ $book->penerbit }}</div>
        <div><b>Tahun:</b> {{ $book->tahun }}</div>
        <div><b>ISBN:</b> {{ $book->isbn ?? '-' }}</div>
      </div>
      <div class="sinopsis">
        <b>Sinopsis</b><br>{{ $book->sinopsis }}
      </div>

<div class="action">
    <!-- Jika user sudah login → buka form peminjaman -->
    @if(!$isGuestView)
      @if($canBorrow)
      <a href="{{ route('peminjaman.create', $book->id) }}" class="pinjam">📚 Pinjam Buku</a>
      @else
      @php
        $borrowBlockedMessage = (int) $book->stok < 1
          ? 'Stok buku habis'
          : 'Kembalikan buku terlebih dahulu';
      @endphp
      <a href="#" class="pinjam disabled" onclick="showBorrowLimitAlert('{{ $borrowBlockedMessage }}'); return false;">📚 Pinjam Buku</a>
      @endif
      @if($isWishlisted)
        <form method="POST" action="{{ route('koleksi_buku.destroy', $book->id) }}">
          @csrf
          @method('DELETE')
          <button class="fav-btn active" type="submit">Hapus Favorit</button>
        </form>
      @else
        <form method="POST" action="{{ route('koleksi_buku.store', $book->id) }}">
          @csrf
          <button class="fav-btn" type="submit">Simpan Favorit</button>
        </form>
      @endif
    @else
      <a href="{{ route('login') }}" class="pinjam">📚 Pinjam Buku</a>
      <a href="{{ route('login') }}" class="fav-btn">Simpan Favorit</a>
    @endif
</div>
    </div>
  </div>
</div>

<!-- ULASAN -->
<div class="ulasan">
  <h2>Ulasan Pembaca</h2>
  <div class="ulasan-list">

    @forelse($book->reviews as $review)
      <div class="review-card">
        <div class="review-header">
          @php
            $nama = $review->user->nama_lengkap ?? $review->user->name ?? $review->user->email;
          @endphp
          <div class="avatar">{{ strtoupper(substr($nama, 0, 1)) }}</div>
          <div>
            <div class="review-name">{{ $nama }}</div>
            <div class="review-rating">
              @for($i = 1; $i <= 5; $i++)
                @if($i <= $review->rating)
                  ★
                @else
                  <span>★</span>
                @endif
              @endfor
            </div>
          </div>
        </div>
        <div class="review-text">{{ $review->comment ?? 'Tidak ada komentar.' }}</div>
      </div>
    @empty
      <div class="review-card" style="text-align:center;color:#777">Belum ada ulasan.</div>
    @endforelse

  </div>
</div>

<div id="borrowLimitAlert" class="borrow-alert" role="alert" aria-live="polite">Kembalikan buku terlebih dahulu</div>

@if(session('error'))
<script src="{{ asset('js/buku-detail.js') }}"></script>
@endif

<script src="{{ asset('js/buku-detail.js') }}"></script>

</body>
</html>











