<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Bukti Peminjaman - {{ $peminjaman->kode }}</title>
  @php
    $cssPath = public_path('css/peminjaman-struk-pdf.css');
    $pdfCss = file_exists($cssPath) ? file_get_contents($cssPath) : '';
  @endphp
  <style>
    {!! $pdfCss !!}
  </style>
</head>
<body>
  @php
    $statusClass = match($peminjaman->status) {
      'pending' => 'status status-pending',
      'confirmed' => 'status status-confirmed',
      'returned' => 'status status-returned',
      default => 'status status-other',
    };
  @endphp

  <div class="sheet">
    <div class="topbar"></div>
    <div class="content">
      <div class="header">
        <h1 class="title">Bukti Peminjaman</h1>
        <div class="subtitle">DIGIPUS - Sistem Perpustakaan Digital</div>
      </div>

      <div class="kode-box">
        <div class="kode-label">KODE PEMINJAMAN</div>
        <div class="kode-value">{{ $peminjaman->kode }}</div>
      </div>

      <table class="detail">
        <tr><td>Peminjam</td><td>{{ $peminjaman->user->nama_lengkap ?? $peminjaman->user->email }}</td></tr>
        <tr><td>Email</td><td>{{ $peminjaman->user->email }}</td></tr>
        <tr><td>Nomor Telepon</td><td>{{ $peminjaman->no_telepon ?? '-' }}</td></tr>
        <tr><td>Alamat</td><td>{{ $peminjaman->alamat ?? '-' }}</td></tr>
        <tr><td>Buku</td><td>{{ $peminjaman->book->judul }}</td></tr>
        <tr><td>Penulis</td><td>{{ $peminjaman->book->penulis ?? 'Tidak diketahui' }}</td></tr>
        <tr><td>Tanggal Pinjam</td><td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y H:i') }}</td></tr>
        <tr><td>Durasi</td><td>{{ $peminjaman->durasi }} {{ $peminjaman->durasi_satuan ?? 'hari' }}</td></tr>
        <tr><td>Jatuh Tempo</td><td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d M Y') }}</td></tr>
        <tr><td>Status</td><td><span class="{{ $statusClass }}">{{ ucfirst($peminjaman->status) }}</span></td></tr>
      </table>

      <div class="note">
        Penting: Simpan dokumen ini dan kode peminjaman untuk kebutuhan verifikasi pengembalian buku.
      </div>
      <div class="footer">Dicetak pada {{ now()->format('d M Y H:i:s') }}</div>
    </div>
  </div>
</body>
</html>


