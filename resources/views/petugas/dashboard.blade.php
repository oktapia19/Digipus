<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Petugas - DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/petugas-dasbor.css') }}">
</head>

<body>

<!-- HEADER -->
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

<!-- HERO -->
@php
  $petugasLogin = auth('petugas')->user();
  $petugasName = $petugasLogin->nama ?? $petugasLogin->name ?? $petugasLogin->username ?? $petugasLogin->email ?? 'Petugas';
@endphp
<div class="hero">
  <h1>Selamat datang petugas, {{ $petugasName }}</h1>
  <p>Konfirmasi peminjaman & pengembalian buku</p>
</div>

<!-- STATS -->
<div class="stats">
  <div class="stat-box">
    <div class="stat-icon bg-pinjam"><i class="fa-solid fa-book-open"></i></div>
    <div>
      <h3>{{ $borrowedBooks }}</h3>
      <p>Buku Dipinjam</p>
    </div>
  </div>

  <div class="stat-box">
    <div class="stat-icon bg-kembali"><i class="fa-solid fa-rotate-left"></i></div>
    <div>
      <h3>{{ $returnedBooks }}</h3>
      <p>Buku Dikembalikan</p>
    </div>
  </div>

  <div class="stat-box">
    <div class="stat-icon bg-telat"><i class="fa-solid fa-clock"></i></div>
    <div>
      <h3>{{ $lateBooks }}</h3>
      <p>Buku Telat</p>
    </div>
  </div>
</div>

<!-- SECTION BUTTONS PENGEMBALIAN & EXPORT -->
@if($returnedBooks > 0)
<div class="no-print" style="margin:28px;padding:0;display:flex;gap:12px;align-items:center">
  <button class="btn" type="button" onclick="openModalPengembalianBuku()">
    <i class="fa-solid fa-rotate-left"></i> Pengembalian
  </button>
  <button class="btn btn-export" type="button" onclick="window.print()">
    <i class="fa-solid fa-print"></i> Print
  </button>
</div>
@endif

<!-- TABLE KONFIRMASI -->
<div class="section">
  <div class="no-print" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h3 style="color:var(--primary)">Peminjam Buku</h3>
    <a href="{{ route('petugas.denda.index') }}" class="btn btn-sm">
      <i class="fa-solid fa-money-bill-wave"></i> Lihat Denda
    </a>
  </div>
  <div class="print-area">
    <div class="print-header">
      <h1>Laporan Data Peminjaman Buku DIGIPUS</h1>
      <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Buku</th>
          <th>Tgl Pinjam</th>
          <th>Jatuh Tempo</th>
          <th>Status</th>
          <th class="col-bukti">Bukti</th>
          <th class="col-action">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($peminjamans ?? [] as $p)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->user->nama_lengkap ?? $p->user->email }}</td>
            <td>{{ $p->book->judul }}</td>
            <td>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d M Y') }}</td>
            <td><span style="color:{{ $p->status === 'pending' ? '#f57c00' : ($p->status === 'confirmed' ? '#2e7d32' : ($p->status === 'waiting_return' ? '#1976d2' : '#999')) }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
            <td class="col-bukti">
              <button class="btn btn-sm" onclick="bukaBuktiModal({{ $p->id }})">Lihat Bukti</button>
            </td>
            <td class="col-action" style="display:flex;gap:8px;align-items:center">
              @if($p->status === 'pending')
                <button class="btn btn-success btn-sm" onclick="konfirmasiLangsung({{ $p->id }})">Konfirmasi</button>
                <button class="btn btn-danger btn-sm" onclick="batalkanLangsung({{ $p->id }})">Batalkan</button>
              @elseif($p->status === 'confirmed')
                <button class="btn btn-success btn-sm" onclick="konfirmasiLangsung({{ $p->id }})">Konfirmasi</button>
              @elseif($p->status === 'waiting_return')
                <button class="btn btn-info btn-sm" onclick="openVerifikasiReturn({{ $p->id }}, '{{ $p->kode }}')">Verifikasi</button>
              @elseif($p->status === 'returned')
                <span class="btn btn-sm btn-soft">Dikembalikan</span>
              @else
                <span style="color:#999;font-size:11px;padding:6px">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align:center">Tidak ada peminjaman</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL BUKTI RECEIPT -->
<div id="modalBuktiReceipt" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:3000;justify-content:center;align-items:center;padding:20px">
  <div style="background:#fff;padding:32px;border-radius:16px;max-width:500px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 50px rgba(0,0,0,.15);text-align:center">
    <h2 style="color:var(--primary);margin-bottom:20px">📄 Bukti Pengembalian</h2>
    
    <!-- KODE BESAR -->
    <div style="background:linear-gradient(135deg,var(--secondary),var(--primary));padding:30px;border-radius:12px;margin-bottom:24px;color:#fff">
      <p style="margin:0;opacity:0.9;font-size:13px">Kode Pengembalian</p>
      <div id="kodeDisplay" style="font-size:48px;font-weight:700;letter-spacing:2px;margin:12px 0;font-family:monospace">PJM001</div>
      <button onclick="salinKode()" style="background:rgba(255,255,255,.2);border:2px solid #fff;color:#fff;padding:8px 16px;border-radius:8px;cursor:pointer;font-weight:600;font-size:12px;transition:.2s" onmouseover="this.style.background='rgba(255,255,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">📋 Salin Kode</button>
    </div>
    
    <!-- DETAIL -->
    <div id="buktiDetail" style="text-align:left;font-size:14px"></div>
    
    <!-- BUTTONS -->
    <div style="display:flex;gap:10px;margin-top:24px;justify-content:flex-end">
      <button onclick="tutupBuktiReceipt()" style="padding:10px 20px;background:#eee;border:none;border-radius:8px;cursor:pointer;font-weight:600">Tutup</button>
    </div>
  </div>
</div>

<!-- MODAL DETAIL PEMINJAMAN -->
<div id="modalBukti" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:3000;justify-content:center;align-items:center">
  <div style="background:#fff;padding:28px;border-radius:16px;max-width:480px;max-height:80vh;overflow-y:auto;box-shadow:0 15px 35px rgba(0,0,0,.2)">
    <h2 style="color:var(--primary);margin-bottom:12px">📋 Detail Peminjaman</h2>
    <div id="modalContent" style="margin-top:16px;font-size:14px"></div>
    <div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end">
      <button onclick="tutupBukti()" style="padding:8px 16px;background:#eee;border:none;border-radius:8px;cursor:pointer;font-weight:600">Tutup</button>
    </div>
  </div>
</div>

<!-- MODAL VERIFIKASI RETURN -->
<div id="modalVerifikasiReturn" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:3000;justify-content:center;align-items:center">
  <div style="background:#fff;padding:32px;border-radius:16px;max-width:420px;box-shadow:0 20px 50px rgba(0,0,0,.15)">
    <!-- HEADER -->
    <div style="text-align:center;margin-bottom:24px">
      <div style="font-size:40px;margin-bottom:12px">🔐</div>
      <h2 style="color:var(--primary);font-size:22px;margin:0;margin-bottom:8px">Kode Pengembalian</h2>
      <p style="color:#666;font-size:14px;margin:0">Masukkan kode pengembalian buku untuk melanjutkan proses</p>
    </div>
    
    <!-- FORM -->
    <form id="formVerifikasiReturn" style="display:flex;flex-direction:column;gap:16px">
      @csrf
      <div>
        <label style="display:block;font-size:12px;color:#999;margin-bottom:6px;font-weight:600">Masukkan Kode dari Bukti Pengembalian</label>
        <input type="text" id="kodeVerifikasi" name="kode_verifikasi" placeholder="CONTOH: ABCDEF12" style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;font-family:monospace;font-weight:600;letter-spacing:1px;transition:.2s;text-transform:uppercase" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e0e0e0'" />
      </div>
      <div>
        <label style="display:block;font-size:12px;color:#999;margin-bottom:6px;font-weight:600">Tanggal Pengembalian</label>
        <input type="date" id="tanggalPengembalian" name="tanggal_pengembalian" style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;transition:.2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e0e0e0'" />
      </div>
      <div>
        <label style="display:block;font-size:12px;color:#999;margin-bottom:6px;font-weight:600">Kondisi Buku</label>
        <select id="kondisiBuku" name="kondisi_buku" style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;transition:.2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e0e0e0'">
          <option value="baik">Baik</option>
          <option value="rusak">Rusak (+Rp 5.000)</option>
        </select>
      </div>
      
      <!-- BUTTONS -->
      <div style="display:flex;gap:10px;margin-top:8px">
        <button type="button" onclick="closeVerifikasiReturn()" style="flex:1;padding:12px 16px;background:var(--primary);color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;transition:.2s" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Batal</button>
        <button type="submit" style="flex:1;padding:12px 16px;background:#2e7d32;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;transition:.2s" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Konfirmasi</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/petugas-dasbor.js') }}"></script>

@include('petugas.navbar')

</body>
</html>









