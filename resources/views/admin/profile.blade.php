<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Admin | DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-profil.css') }}">
</head>

<body>

<div class="header">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}">
    <h2>DIGIPUS</h2>
  </div>
  <div class="header-right">
    @include('components.notification-bell')
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button class="header-action">
        <i class="fa-solid fa-right-from-bracket"></i> LOG OUT
      </button>
    </form>
  </div>
</div>

@if(session('success'))
  <div class="flash success auto-dismiss-alert">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="flash error auto-dismiss-alert">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="flash error auto-dismiss-alert">{{ $errors->first() }}</div>
@endif

<div class="profile-banner">
  <form action="{{ route('admin.profile.photo') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="profile-photo">
      <img
        src="{{ $admin->photo
              ? asset('storage/profile/admin/'.$admin->photo.'?v='.time())
              : asset('images/default-avatar.png') }}"
        alt="Avatar Admin">
      <label for="admin-photo-input">
        <i class="fa-solid fa-camera"></i>
      </label>
      <input
        type="file"
        name="photo"
        id="admin-photo-input"
        hidden
        accept="image/*"
        onchange="this.form.submit()">
    </div>
  </form>
  <div class="profile-text">
    <h2>{{ $admin->name }}</h2>
    <p>{{ $admin->email }}</p>
    <div class="role">Admin Digipus</div>
  </div>
</div>

<div class="content">
  <div class="card">
    <h3>Informasi Akun</h3>
    <div class="info-grid">
      <div class="info-box">
        <span>Nama</span>
        <p>{{ $admin->name }}</p>
      </div>
      <div class="info-box">
        <span>Email</span>
        <p>{{ $admin->email }}</p>
      </div>
      <div class="info-box">
        <span>Tanggal Bergabung</span>
        <p>{{ $admin->created_at->format('d F Y') }}</p>
      </div>
    </div>
  </div>

  <div class="card">
    <h3>Ringkasan Admin</h3>
    <div class="summary-grid">
      <div class="summary-mini">
        <div class="label">Total Petugas</div>
        <div class="num">{{ $petugasCount }}</div>
      </div>
      <div class="summary-mini">
        <div class="label">Jumlah User</div>
        <div class="num">{{ $userCount }}</div>
      </div>
      <div class="summary-mini">
        <div class="label">Buku Dipinjam</div>
        <div class="num">{{ $borrowedBooks }}</div>
      </div>
      <div class="summary-mini">
        <div class="label">Buku Dikembalikan</div>
        <div class="num">{{ $returnedBooks }}</div>
      </div>
      <div class="summary-mini">
        <div class="label">Buku Telat</div>
        <div class="num">{{ $lateBooks }}</div>
      </div>
    </div>
  </div>
</div>
@include('admin.navbar')

<script src="{{ asset('js/admin-profil.js') }}"></script>

</body>
</html>



