<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Detail Peminjaman</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/peminjaman-detail.css') }}">
</head>
<body>
  <div class="card">
    <div class="header">📋 Detail Peminjaman</div>
    <div class="row">
      <span class="label">Kode:</span>
      <span class="value">{{ $peminjaman->kode }}</span>
    </div>
    <div class="row">
      <span class="label">Buku:</span>
      <span class="value">{{ $peminjaman->book->judul }}</span>
    </div>
    <div class="row">
      <span class="label">Peminjam:</span>
      <span class="value">{{ $peminjaman->user->nama_lengkap ?? $peminjaman->user->email }}</span>
    </div>
    <div class="row">
      <span class="label">Durasi:</span>
      <span class="value">{{ $peminjaman->durasi }} {{ $peminjaman->durasi_satuan ?? 'hari' }}</span>
    </div>
    <div class="row">
      <span class="label">Jatuh Tempo:</span>
      <span class="value">{{ $peminjaman->tanggal_kembali }}</span>
    </div>
    <div class="row">
      <span class="label">Kontak:</span>
      <span class="value">{{ $peminjaman->no_telepon }}<br>{{ $peminjaman->alamat }}</span>
    </div>
    <div class="row">
      <span class="label">Status:</span>
      <span class="badge {{ $peminjaman->status === 'pending' ? 'pending' : 'confirmed' }}">{{ ucfirst($peminjaman->status) }}</span>
    </div>
    <a href="{{ route('peminjaman.index') }}" class="back">← Kembali ke Riwayat</a>
  </div>
</body>
</html>


