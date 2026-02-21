<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Petugas - Data User</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/petugas-pengguna-daftar.css') }}">
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

<div class="hero">
  <h1>Data User</h1>
  <p>Lihat data user dan export laporan</p>
</div>

<div class="main-content">
  <div class="export-actions no-print">
    <button class="btn" type="button" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Print
    </button>
  </div>

  <div class="table-container print-area">
    <div class="print-header">
      <h1>Laporan Data Pengguna DIGIPUS</h1>
      <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
    </div>
    <table>
      <thead>
        <tr>
          <th class="col-no">No</th>
          <th class="col-photo">Profil</th>
          <th class="col-name">Nama</th>
          <th class="col-email">Email</th>
          <th class="col-role">Role</th>
          <th class="col-date">Tanggal Daftar</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td class="col-no">{{ $loop->iteration }}</td>
          <td class="col-photo">
            @if($user->photo)
              <img class="user-photo" src="{{ asset('storage/profile/' . $user->photo) }}" alt="Profil {{ $user->name }}">
            @else
              <span class="user-photo initial">{{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}</span>
            @endif
          </td>
          <td class="col-name"><strong>{{ $user->name }}</strong></td>
          <td class="col-email">{{ $user->email }}</td>
          <td class="col-role">
            <span class="role-badge">User</span>
          </td>
          <td class="col-date">{{ $user->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center;padding:40px;">
            Belum ada user
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@include('petugas.navbar')
</body>
</html>








