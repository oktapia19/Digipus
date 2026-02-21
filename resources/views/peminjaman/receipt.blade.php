<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bukti Peminjaman - {{ $peminjaman->kode }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/peminjaman-struk.css') }}">
</head>
<body>

<div class="receipt">
  <!-- HEADER -->
  <div class="receipt-header">
    <h1>📄 Bukti Peminjaman</h1>
    <p>DIGIPUS - Sistem Perpustakaan Digital</p>
  </div>
  
  <!-- KODE PEMINJAMAN -->
  <div class="receipt-kode">
    <div class="label">KODE PEMINJAMAN</div>
    <div class="kode" id="kodeDisplay">{{ $peminjaman->kode }}</div>
    <button class="copy-btn" onclick="copyKode()">
      <i class="fa-solid fa-copy"></i> Salin Kode
    </button>
  </div>
  
  <!-- DETAIL PEMINJAMAN -->
  <div class="receipt-row">
    <span class="receipt-label">Peminjam:</span>
    <span class="receipt-value">{{ $peminjaman->user->nama_lengkap ?? $peminjaman->user->email }}</span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Email:</span>
    <span class="receipt-value">{{ $peminjaman->user->email }}</span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Nomor Telepon:</span>
    <span class="receipt-value">{{ $peminjaman->no_telepon ?? '-' }}</span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Alamat:</span>
    <span class="receipt-value">{{ $peminjaman->alamat ?? '-' }}</span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Buku:</span>
    <span class="receipt-value"><strong>{{ $peminjaman->book->judul }}</strong></span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Penulis:</span>
    <span class="receipt-value">{{ $peminjaman->book->penulis ?? 'Tidak diketahui' }}</span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Tanggal Pinjam:</span>
    <span class="receipt-value">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y H:i') }}</span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Durasi:</span>
    <span class="receipt-value">{{ $peminjaman->durasi }} {{ $peminjaman->durasi_satuan ?? 'hari' }}</span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Jatuh Tempo:</span>
    <span class="receipt-value"><strong style="color:#d32f2f">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d M Y') }}</strong></span>
  </div>
  
  <div class="receipt-row">
    <span class="receipt-label">Status:</span>
    <span class="receipt-value">
      <span style="color:{{ $peminjaman->status === 'pending' ? '#f57c00' : ($peminjaman->status === 'confirmed' ? '#2e7d32' : '#999') }};font-weight:600">
        {{ ucfirst($peminjaman->status) }}
      </span>
    </span>
  </div>
  
  <!-- CATATAN -->
  <div class="receipt-note">
    <i class="fa-solid fa-info-circle"></i> <strong>Penting:</strong> Salin dan simpan kode peminjaman ini. Gunakan kode ini ketika mengembalikan buku untuk verifikasi admin.
  </div>
  
  <!-- FOOTER -->
  <div class="receipt-footer">
    <p>Dicetak pada {{ now()->format('d M Y H:i:s') }}</p>
  </div>
</div>

<script src="{{ asset('js/popup-indonesia.js') }}"></script>
<script src="{{ asset('js/peminjaman-struk.js') }}"></script>

</body>
</html>


