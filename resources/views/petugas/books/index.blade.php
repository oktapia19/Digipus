<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Petugas - Data Buku</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/petugas-buku-daftar.css') }}">
</head>

<body>

<div class="header">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}" alt="Logo DIGIPUS">
    <h2>DIGIPUS</h2>
  </div>
  <div class="header-right">
    @include('components.notification-bell')
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="header-action">LOG OUT</button>
    </form>
  </div>
</div>

<div class="page-banner">
  <h1>Daftar Buku</h1>
  <p>Panel Petugas</p>
</div>

<div class="main-content">

<div class="no-print" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
  <a href="{{ route('petugas.books.create') }}" class="btn">
    <i class="fa-solid fa-plus"></i> Tambah Buku
  </a>
  <button class="btn" type="button" onclick="window.print()">
    <i class="fa-solid fa-print"></i> Print
  </button>
</div>

@if(session('success'))
  <div class="alert-success auto-dismiss-alert">
    {{ session('success') }}
  </div>
@endif

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
  <th>Tahun</th>
  <th>Kategori</th>
  <th>Stok</th>
  <th>Sinopsis</th>
  <th>Status</th>
  <th class="col-action">Aksi</th>
</tr>
</thead>

<tbody>
@forelse($books as $book)
<tr>
  <td>{{ $loop->iteration }}</td>
  <td>
    <img src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://via.placeholder.com/45x68' }}">
  </td>
  <td>{{ $book->judul }}</td>
  <td>{{ $book->isbn ?? '-' }}</td>
  <td>{{ $book->penulis ?? '-' }}</td>
  <td>{{ $book->tahun ?? '-' }}</td>
  <td>
    @foreach($book->kategoris as $kat)
      <span class="kategori-badge">{{ $kat->name }}</span>
    @endforeach
  </td>
  <td>{{ $book->stok }}</td>
  <td>{{ Str::limit($book->sinopsis,120) }}</td>

  <td>
    @php
      $statusLabels = [
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'menunggu_hapus' => 'Menunggu Hapus',
      ];
    @endphp
    <span class="status {{ $book->status }}">
      {{ $statusLabels[$book->status] ?? ucfirst(str_replace('_',' ', $book->status)) }}
    </span>
  </td>

  <td class="col-action">
    <div class="action-buttons">

      {{-- EDIT --}}
      <a href="{{ route('petugas.books.edit',$book->id) }}"
         class="action-btn edit {{ in_array($book->status, ['menunggu_konfirmasi', 'menunggu_hapus']) ? 'disabled' : '' }}"
         title="Edit">
        <i class="fa fa-pen"></i>
      </a>

      {{-- HAPUS (REQUEST) --}}
      <form action="{{ route('petugas.books.destroy',$book->id) }}"
            method="POST"
            data-popup-confirm
            data-popup-title="Konfirmasi Hapus"
            data-popup-message="Yakin mau menghapus buku ini? Permintaan akan dikirim ke admin untuk persetujuan.">
        @csrf
        @method('DELETE')
        <button class="action-btn delete {{ in_array($book->status, ['menunggu_konfirmasi', 'menunggu_hapus']) ? 'disabled' : '' }}"
                title="Hapus">
          <i class="fa fa-trash"></i>
        </button>
      </form>

    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="11" style="text-align:center;padding:30px;">
    Belum ada buku
  </td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>

@include('petugas.navbar')

<script src="{{ asset('js/petugas-buku-daftar.js') }}"></script>

</body>
</html>









