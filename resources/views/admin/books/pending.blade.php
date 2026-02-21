<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Admin - Konfirmasi Buku</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-buku-tertunda.css') }}">
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
  <h1>Konfirmasi Perubahan Buku</h1>
  <p>Setujui atau tolak permintaan tambah, edit, dan hapus buku</p>
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
      <p>Setujui atau tolak permintaan tambah, edit, dan hapus buku</p>
    </a>
  </div>
  <!-- TOMBOL EXPORT PDF -->
  <div class="export-actions no-print">
    <button class="btn btn-export" type="button" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Print
    </button>
  </div>

<!-- TABEL BUKU PENDING -->
  <div class="table-container print-area">
    <div class="print-header">
      <h1>Laporan Buku Menunggu Konfirmasi DIGIPUS</h1>
      <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Cover</th>
          <th>Judul</th>
          <th>Penulis</th>
          <th>Penerbit</th>
          <th>Tahun</th>
          <th>Kategori</th>
          <th>Stok</th>
                    <th>Status</th>
          <th class="col-action">Aksi</th>
        </tr>
      </thead>
      <tbody>
      @forelse($pendingBooks as $book)
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
          <td>{{ $book->penulis ?? '-' }}</td>
          <td>{{ $book->penerbit ?? '-' }}</td>
          <td>{{ $book->tahun ?? '-' }}</td>
          <td>
            @foreach($book->kategoris as $kat)
              <span class="kategori-badge">{{ $kat->name }}</span>
            @endforeach
          </td>
          <td>{{ $book->stok }}</td>
                    @php
                      $statusLabels = [
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'menunggu_hapus' => 'Menunggu Hapus',
                      ];
                    @endphp
                    <td>{{ $statusLabels[$book->status] ?? ucfirst($book->status) }}</td>
          <td class="col-action">
            <div class="action-buttons">
              @if($book->status === 'menunggu_konfirmasi')
                <form action="{{ route('admin.books.approve', $book->id) }}" method="POST">
                  @csrf
                  <button class="action-btn approve" type="submit" title="Approve">
                    <i class="fa-solid fa-check"></i>
                  </button>
                </form>
                <form action="{{ route('admin.books.reject', $book->id) }}" method="POST">
                  @csrf
                  <button class="action-btn reject" type="submit" title="Reject">
                    <i class="fa-solid fa-xmark"></i>
                  </button>
                </form>
              @elseif($book->status === 'disetujui')
                <i class="fa-solid fa-check-circle" style="color: #10B981; font-size: 20px;" title="Approved"></i>
              @elseif($book->status === 'ditolak')
                <i class="fa-solid fa-times-circle" style="color: #EF4444; font-size: 20px;" title="Rejected"></i>
              @elseif($book->status === 'menunggu_hapus')
                <form action="{{ route('admin.books.approve', $book->id) }}" method="POST">
                  @csrf
                  <button class="action-btn approve" type="submit" title="Setujui Hapus">
                    <i class="fa-solid fa-check"></i>
                  </button>
                </form>
                <form action="{{ route('admin.books.reject', $book->id) }}" method="POST">
                  @csrf
                  <button class="action-btn reject" type="submit" title="Tolak Hapus">
                    <i class="fa-solid fa-xmark"></i>
                  </button>
                </form>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="10" style="text-align:center;padding:40px;color:#666;">Belum ada permintaan konfirmasi buku</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

</div>

@include('admin.navbar')

<script src="{{ asset('js/admin-buku-tertunda.js') }}"></script>

</body>
</html>










