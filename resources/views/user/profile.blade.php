<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil | DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/pengguna-profil.css') }}">
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
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button class="header-action">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </button>
    </form>
  </div>
</div>

@if(session('success'))
  <div class="flash-alert success auto-dismiss-alert">
    {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="flash-alert error auto-dismiss-alert">
    {{ session('error') }}
  </div>
@endif
@if($errors->any())
  <div class="flash-alert error auto-dismiss-alert">
    {{ $errors->first() }}
  </div>
@endif

<!-- PROFILE -->
<div class="profile-banner">
  <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
    @csrf
 <div class="profile-photo">
  <img 
    src="{{ $user->photo 
          ? asset('storage/profile/'.$user->photo.'?v='.time()) 
          : asset('images/default-avatar.png') }}">
  <label for="photo-input">
    <i class="fa-solid fa-camera"></i>
  </label>
  <input type="file" name="photo" id="photo-input" hidden accept="image/*" onchange="this.form.submit()">
</div>

  </form>

  <div class="profile-text">
    <h2>{{ $user->name }}</h2>
    <p>{{ $user->email }}</p>
    <div class="role">User Digipus</div>
  </div>
</div>

<!-- CONTENT -->
<div class="content">

  <div class="card">
    <div class="card-header">
      <h3>Informasi Akun</h3>
      <button class="btn-ghost" type="button" onclick="openEditModal()">Edit</button>
    </div>
    <div class="info-grid">
      <div class="info-box">
        <span>Nama Lengkap</span>
        <p>{{ $user->name }}</p>
      </div>
      <div class="info-box">
        <span>Email</span>
        <p>{{ $user->email }}</p>
      </div>
      <div class="info-box">
        <span>Alamat</span>
        <p>{{ $user->alamat ?: '-' }}</p>
      </div>
      <div class="info-box">
        <span>No. Telepon</span>
        <p>{{ $user->no_telepon ?: '-' }}</p>
      </div>
      <div class="info-box">
        <span>Tanggal Bergabung</span>
        <p>{{ $user->created_at->format('d F Y') }}</p>
      </div>
    </div>
  </div>

  <div class="card">
    <h3>Aktivitas Peminjaman</h3>

    <div class="activity-box">
      <div class="activity-left">
        <div class="activity-icon">📚</div>
        <div>
          <span>Total</span>
          <p>Buku Dipinjam</p>
        </div>
      </div>
    <div class="activity-count">{{ $borrowedCount }}</div>
    </div>

    <div class="activity-box">
      <div class="activity-left">
        <div class="activity-icon">✅</div>
        <div>
          <span>Selesai</span>
          <p>Buku Dikembalikan</p>
        </div>
      </div>
    <div class="activity-count">{{ $returnedCount }}</div>
    </div>
  </div>

</div>

<div class="modal" id="editProfileModal" data-auto-open="{{ $errors->any() ? '1' : '0' }}">
  <div class="modal-card">
    <div class="modal-head">
      <h3>Edit Profil User</h3>
      <button class="modal-close" type="button" onclick="closeEditModal()">X</button>
    </div>
    <form action="{{ route('profile.update') }}" method="POST">
      @csrf
      <div class="form-group">
        <label>Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
      </div>
      <div class="form-group">
        <label>Alamat</label>
        <input type="text" name="alamat" value="{{ old('alamat', $user->alamat) }}">
      </div>
      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}">
      </div>
      <div class="form-group">
        <label>Password Lama</label>
        <input type="password" name="current_password">
      </div>
      <div class="form-group">
        <label>Password Baru</label>
        <input type="password" name="password">
        <div class="helper">Kosongkan jika tidak ingin ganti password.</div>
      </div>
      <div class="form-group">
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="password_confirmation">
      </div>
      <button class="btn-primary" type="submit">Simpan Perubahan</button>
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
    <i class="fa-solid fa-book"></i>
    Buku
  </a>
  <a href="{{ route('peminjaman.index') }}">
    <i class="fa-solid fa-clock-rotate-left"></i>
    Riwayat
  </a>
  <a href="{{ route('koleksi_buku.index') }}">
    <i class="fa-solid fa-heart"></i>
    Koleksi
  </a>
  <a class="active">
    <i class="fa-solid fa-user"></i>
    Profil
  </a>
</div>

<script src="{{ asset('js/pengguna-profil.js') }}"></script>

</body>
</html>



