<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Admin - Data Petugas</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-petugas-daftar.css') }}">
</head>

<body>

<header class="header">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}" alt="Logo DIGIPUS">
    <h2>DIGIPUS</h2>
  </div>
  <div class="header-right">
    @include('components.notification-bell')
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout">LOG OUT</button>
    </form>
  </div>
</header>

@if(session('success'))
<div class="alert auto-dismiss-alert">{{ session('success') }}</div>
@endif

<div class="hero">
  <h1>Data Petugas</h1>
  <p>Kelola data petugas dengan mudah</p>
</div>

<div class="main-content">
  <div class="no-print" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:16px;">    <a href="{{ route('admin.petugas.create') }}" class="btn">
      <i class="fa-solid fa-plus"></i> Tambah Petugas
    </a>
    <button class="btn btn-export" type="button" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Print
    </button>
</div>

  <div class="table-container print-area">
    <div class="print-header">
      <h1>Laporan Data Petugas DIGIPUS</h1>
      <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
    </div>
    <table>
      <thead>
        <tr>
          <th class="col-no">No</th>
          <th class="col-photo">Foto</th>
          <th class="col-name">Nama</th>
          <th class="col-email">Email</th>
          <th class="col-pass">Sandi</th>
          <th class="col-alamat">Alamat</th>
          <th class="col-telp">No. Telepon</th>
          <th class="col-date">Tanggal Daftar</th>
          <th class="col-action">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($petugas as $p)
        <tr>
          <td class="col-no">{{ $loop->iteration }}</td>
          <td class="col-photo">
            @if($p->photo)
              <img class="petugas-photo" src="{{ asset('storage/profile/petugas/' . $p->photo) }}" alt="Foto {{ $p->nama }}">
            @else
              <span class="petugas-photo petugas-photo-inisial">{{ strtoupper(substr($p->nama, 0, 1)) }}</span>
            @endif
          </td>
          <td class="col-name"><strong>{{ $p->nama }}</strong></td>
          <td class="col-email">{{ $p->email }}</td>
          <td class="col-pass">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</td>
          <td class="col-alamat">{{ $p->alamat ?? '-' }}</td>
          <td class="col-telp">{{ $p->no_telepon ?? '-' }}</td>
          <td class="col-date">{{ $p->created_at->format('d M Y') }}</td>
          <td class="col-action">
            <div class="action-group">
              <a href="{{ route('admin.petugas.edit', $p->id) }}" class="btn-edit">
                <i class="fa-solid fa-pen"></i>
              </a>

              <form action="{{ route('admin.petugas.destroy', $p->id) }}" method="POST"
                    data-popup-confirm
                    data-popup-title="Konfirmasi Hapus"
                    data-popup-message="Yakin hapus petugas ini?">
                @csrf
                @method('DELETE')
                <button class="btn-delete">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" style="text-align:center;padding:40px;">
            Belum ada petugas
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@include('admin.navbar')
<script src="{{ asset('js/admin-petugas-daftar.js') }}"></script>
</body>
</html>









