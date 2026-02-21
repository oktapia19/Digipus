<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Riwayat Peminjaman</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/peminjaman-daftar.css') }}">
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}">
    <h2>DIGIPUS</h2>
  </div>
<div class="header-right">
    @include('components.notification-bell')
    @auth
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="header-action"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
      </form>
    @endauth
  </div>
</div>

<!-- HERO -->
<div class="page">
  <div class="hero">
    <h1>Riwayat Peminjaman</h1>
    <p>Pantau semua buku yang sedang dan sudah kamu pinjam</p>
  </div>

  <!-- STATS -->
  <div class="stats">
    <div class="stat">
      <div class="stat-icon">📚</div>
      <div class="stat-num">{{ $totalPinjam }}</div>
      <div class="stat-text">Total buku yang dipinjam</div>
    </div>
    <div class="stat">
      <div class="stat-icon">⏳</div>
      <div class="stat-num">{{ $menungguKonfirmasi }}</div>
      <div class="stat-text">Menunggu Persetujuan</div>
    </div>
    <div class="stat">
      <div class="stat-icon">✓</div>
      <div class="stat-num">{{ $sudahDikembalikan }}</div>
      <div class="stat-text">Sudah di kembalikan</div>
    </div>
  </div>

  <!-- DAFTAR PEMINJAMAN -->
  <div class="section">
    <div class="section-title">
      <h3>Daftar Peminjaman</h3>
      <a href="{{ route('koleksi_buku.index') }}" class="btn-kecil" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
        <i class="fa-solid fa-heart"></i> Koleksi Buku
      </a>
    </div>
    @forelse($list as $p)
      <div class="item">
        <div class="item-left">
          <img class="item-cover" src="{{ $p->book->cover ? asset('storage/'.$p->book->cover) : 'https://via.placeholder.com/120x160?text=Cover' }}" alt="{{ $p->book->judul }}">
          <div class="item-info">
            <h4>{{ $p->book->judul }}</h4>
            <div class="item-meta">{{ $p->created_at->format('d F Y') }}</div>
            <div class="item-sub">
              <span><i class="fa-regular fa-calendar"></i> Tanggal pinjam</span>
            </div>
          </div>
        </div>
        <div class="item-right">
          <span class="badge {{ $p->status === 'pending' ? 'badge-pending' : ($p->status === 'confirmed' ? 'badge-confirmed' : ($p->status === 'waiting_return' ? 'badge-waiting' : ($p->status === 'returned' ? 'badge-returned' : 'badge-rejected'))) }}">
            {{ $p->status === 'pending' ? 'Menunggu' : ($p->status === 'confirmed' ? 'Dipinjam' : ($p->status === 'waiting_return' ? 'Kembali' : ($p->status === 'returned' ? 'Selesai' : 'Tolak'))) }}
          </span>
          @if($p->status === 'confirmed')
            <button class="btn-kecil btn-kecil-hijau" onclick="kembalikanBuku({{ $p->id }})" title="Kembalikan Buku">Kembalikan</button>
          @endif
          @if($p->status === 'returned')
            @if(!$p->review)
              <button class="btn-kecil" onclick="openReviewModal({{ $p->id }}, '{{ addslashes($p->book->judul) }}')" title="Beri Ulasan">Beri Ulasan</button>
            @else
              <span class="badge badge-reviewed">Ulasan Terkirim</span>
            @endif
          @endif
          <button class="icon-mata" onclick="lihatBukti({{ $p->id }})" title="Lihat Bukti"><i class="fa-solid fa-eye"></i></button>
        </div>
      </div>
    @empty
      <p style="text-align:center;color:#999;padding:20px">Tidak ada peminjaman.</p>
    @endforelse
  </div>
</div>

<!-- MODAL BUKTI RECEIPT -->
<div id="modalBukti" class="modal" data-peminjaman-id="">
  <div class="modal-content" style="max-width:450px">
    <h3 style="color:var(--primary);margin-bottom:16px;text-align:center">📄 Bukti Pengembalian</h3>
    
    <!-- KODE BESAR -->
    <div style="background:linear-gradient(135deg,var(--secondary),var(--primary));padding:24px;border-radius:12px;margin-bottom:16px;color:#fff;text-align:center">
      <p style="margin:0;opacity:0.9;font-size:12px">Kode Pengembalian</p>
      <div id="kodeBuktiDisplay" style="font-size:40px;font-weight:700;letter-spacing:2px;margin:12px 0;font-family:monospace">PJM001</div>
      <button onclick="salinKodeBukti()" style="background:rgba(255,255,255,.2);border:2px solid #fff;color:#fff;padding:8px 14px;border-radius:8px;cursor:pointer;font-weight:600;font-size:11px;transition:.2s" onmouseover="this.style.background='rgba(255,255,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">📋 Salin Kode</button>
    </div>
    
    <!-- DETAIL -->
    <div id="buktiDetail" style="font-size:13px"></div>
    
    <div style="display:flex;gap:8px;margin-top:16px">
      <button onclick="printBuktiModal()" style="padding:8px 12px;background:#4CAF50;color:#fff;border:none;border-radius:8px;cursor:pointer;flex:1">Cetak PDF</button>
      <button onclick="tutupBuktiModal()" style="padding:8px 12px;background:var(--primary);color:#fff;border:none;border-radius:8px;cursor:pointer;flex:1">Tutup</button>
    </div>
  </div>
</div>

<!-- MODAL ULASAN -->
<div id="modalUlasan" class="modal">
  <div class="modal-content">
    <h3 style="color:var(--primary);margin-bottom:10px;text-align:center">Beri Ulasan</h3>
    <p id="reviewBookTitle" style="text-align:center;font-size:12px;color:#666;margin-bottom:16px"></p>

    <form method="POST" action="{{ route('ulasan_buku.store') }}">
      @csrf
      <input type="hidden" name="peminjaman_id" id="reviewPeminjamanId">

      <div style="margin-bottom:12px">
        <div style="font-size:12px;color:#555;margin-bottom:6px">Rating</div>
        <div class="rating-input">
          <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
          <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
          <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
          <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
          <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
        </div>
      </div>

      <div style="margin-bottom:12px">
        <div style="font-size:12px;color:#555;margin-bottom:6px">Ulasan</div>
        <textarea class="review-textarea" name="comment" placeholder="Ceritakan pengalamanmu..."></textarea>
      </div>

      <button type="submit" class="btn-kecil" style="width:100%">Kirim Ulasan</button>
      <button type="button" class="btn-kecil btn-kecil-outline" style="width:100%;margin-top:8px" onclick="closeReviewModal()">Batal</button>
    </form>
  </div>
</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
  <a href="{{ route('dashboard2') }}">
    <i class="fa-solid fa-house"></i>
    Home
  </a>
  <a href="{{ route('buku.index') }}">
    <i class="fa fa-book"></i>
    Buku
  </a>
  <a href="{{ route('peminjaman.index') }}" class="active">
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
</div>

<script src="{{ asset('js/popup-indonesia.js') }}?v={{ @filemtime(public_path('js/popup-indonesia.js')) }}"></script>
<script src="{{ asset('js/peminjaman-daftar.js') }}?v={{ @filemtime(public_path('js/peminjaman-daftar.js')) }}"></script>

</body>
</html>








