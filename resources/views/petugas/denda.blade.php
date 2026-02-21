<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Denda Buku | DIGIPUS Petugas</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/petugas-denda.css') }}">
</head>
<body>

<div class="hero">
  <h1>Denda Buku</h1>
  <p>Daftar user yang terlambat mengembalikan buku</p>
  <a class="back" href="{{ route('petugas.dashboard') }}">Kembali</a>
</div>

<div class="card">
  <div class="card-head">
    <div style="font-size:13px;color:#666">Denda per hari: <span class="badge">Rp {{ number_format($perHari, 0, ',', '.') }}</span></div>
    <button class="btn no-print" type="button" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Print
    </button>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Buku</th>
        <th>Jatuh Tempo</th>
        <th>Tgl Dikembalikan</th>
        <th>Keadaan Buku</th>
        <th>Terlambat</th>
        <th>Denda</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $p)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $p->user->nama_lengkap ?? $p->user->email }}</td>
          <td>{{ $p->book->judul }}</td>
          <td>{{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d M Y') }}</td>
          <td>
            {{ $p->status === 'returned'
              ? \Carbon\Carbon::parse($p->return_date)->format('d M Y')
              : 'Belum dikembalikan' }}
          </td>
          <td>{{ $p->kondisi_buku ? ucfirst($p->kondisi_buku) : '-' }}</td>
          <td>{{ $p->late_days }} hari</td>
          <td class="money">Rp {{ number_format($p->denda, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align:center;color:#666">Tidak ada peminjaman terlambat</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

</body>
</html>



