<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Admin - Data Buku</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-buku-daftar.css') }}">
</head>

<body>
<!-- TOP BAR -->
<div class="topbar">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}" alt="Logo DIGIPUS">
    <h2>DIGIPUS</h2>
  </div>
  <div class="topbar-right">
    @include('components.notification-bell')
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout-btn">LOG OUT</button>
    </form>
  </div>
</div>

<!-- BANNER -->
<div class="hero">
  <h1>Daftar Buku</h1>
  <p>Kelola data buku dengan cepat dan rapi</p>
</div>

@if(session('success'))
<div class="alert-success auto-dismiss-alert">
  {{ session('success') }}
</div>
@endif

<div class="main-content">

  <!-- SHORTCUT CARDS -->
  <div class="shortcut-cards">
    <a href="{{ route('admin.books.index') }}" class="card">
      <i class="fa-solid fa-book"></i>
      <h2>Buku</h2>
      <p>CRUD buku (lihat, tambah, edit, hapus)</p>
    </a>
    <a href="{{ route('admin.kategori.index') }}" class="card">
      <i class="fa-solid fa-tags"></i>
      <h2>Kategori</h2>
      <p>Kelola kategori buku</p>
    </a>
    <a href="{{ route('admin.books.pending') }}" class="card">
      <i class="fa-solid fa-file-signature"></i>
      <h2>Konfirmasi</h2>
      <p>Konfirmasi buku yang di-upload petugas</p>
    </a>
  </div>

  <!-- TOMBOL TAMBAH BUKU -->
  <a href="{{ route('admin.books.create') }}" class="btn">
    <i class="fa-solid fa-plus"></i> Tambah Buku
  </a>
  <!-- TOMBOL EXPORT PDF -->
  <div class="export-actions no-print">
    <button class="btn btn-export" type="button" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Print
    </button>
  </div>

<!-- TABEL BUKU ADMIN -->
  <div class="table-container print-area">
    <div class="print-header">
      <h1>Laporan Data Buku DIGIPUS</h1>
      <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Cover</th>
          <th>Judul</th>
          <th>ISBN</th>
          <th>Penulis</th>
          <th>Penerbit</th>
          <th>Tahun</th>
          <th>Kategori</th>
          <th>Ulasan</th>
          <th>Stok</th>
          <th>Sinopsis</th>
          <th class="col-action">Aksi</th>
        </tr>
      </thead>
      <tbody>
      @forelse($books as $book)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>
            <a href="{{ route('admin.books.edit', $book->id) }}">
              <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://via.placeholder.com/45x68' }}" alt="Cover">
            </a>
          </td>
          <td>
            <a href="{{ route('admin.books.edit', $book->id) }}" style="text-decoration:none;color:inherit;">
              {{ Str::limit($book->judul,35) }}
            </a>
          </td>
          <td>{{ $book->isbn ?? '-' }}</td>
          <td>{{ $book->penulis ?? '-' }}</td>
          <td>{{ $book->penerbit ?? '-' }}</td>
          <td>{{ $book->tahun ?? '-' }}</td>
          <td>
            @foreach($book->kategoris as $kat)
              <span class="kategori-badge">{{ $kat->name }}</span>
            @endforeach
          </td>
          <td>
            @php
              $reviewCount = $book->reviews->count();
              $avgRating = $reviewCount ? round($book->reviews->avg('rating'), 1) : 0;
              $filledStars = (int) round($avgRating);
              $latestReview = $book->reviews->sortByDesc('created_at')->first();
              $latestReviewer = $latestReview?->user?->nama_lengkap
                ?? $latestReview?->user?->name
                ?? $latestReview?->user?->email;
            @endphp
            <div class="review-wrap">
              <div class="review-stars">
                @for($i = 1; $i <= 5; $i++)
                  {{ $i <= $filledStars ? '★' : '☆' }}
                @endfor
              </div>
              <div class="review-meta">{{ number_format($avgRating, 1) }} ({{ $reviewCount }} ulasan)</div>
              @if($latestReview)
                <div class="review-snippet">
                  <strong>{{ $latestReviewer ?? 'Pengguna' }}</strong>:
                  {{ $latestReview->comment ?: 'Memberi rating tanpa komentar.' }}
                </div>
              @else
                <div class="review-snippet">Belum ada ulasan.</div>
              @endif
            </div>
          </td>
          <td>{{ $book->stok }}</td>
          <td>{{ Str::limit($book->sinopsis,120) }}</td>
          <td class="col-action">
            <div class="action-buttons">
              <a href="{{ route('admin.books.edit', $book->id) }}" class="action-btn edit" title="Edit">
                <i class="fa-solid fa-pen"></i>
              </a>
              <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" style="display:inline;"
                    data-popup-confirm
                    data-popup-title="Konfirmasi Hapus"
                    data-popup-message="Hapus buku ini?">
                @csrf
                @method('DELETE')
                <button class="action-btn delete" type="submit" title="Hapus">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="12" style="text-align:center;padding:40px;color:#666;">Belum ada buku</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

</div>

@include('admin.navbar')

<script src="{{ asset('js/admin-buku-daftar.js') }}"></script>

</body>
</html>










