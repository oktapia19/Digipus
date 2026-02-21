<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Petugas | DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/petugas-profil.css') }}">
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
  <form action="{{ route('petugas.profile.photo') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="profile-photo">
      <img
        src="{{ $petugas->photo
              ? asset('storage/profile/petugas/'.$petugas->photo.'?v='.time())
              : asset('images/default-avatar.png') }}"
        alt="Avatar Petugas">
      <label for="petugas-photo-input">
        <i class="fa-solid fa-camera"></i>
      </label>
      <input
        type="file"
        name="photo"
        id="petugas-photo-input"
        hidden
        accept="image/*"
        onchange="this.form.submit()">
    </div>
  </form>
  <div class="profile-text">
    <h2>{{ $petugas->nama }}</h2>
    <p>{{ $petugas->email }}</p>
    <div class="role">Petugas DIGIPUS</div>
  </div>
</div>

<div class="content">
  <div class="card">
    <div class="card-header">
      <h3>Informasi Akun</h3>
      <button class="btn-ghost" type="button" onclick="openEditModal()">Edit</button>
    </div>

    <div class="info-grid">
      <div class="info-box">
        <span>Nama</span>
        <p>{{ $petugas->nama }}</p>
      </div>
      <div class="info-box">
        <span>Email</span>
        <p>{{ $petugas->email }}</p>
      </div>
      <div class="info-box">
        <span>Alamat</span>
        <p>{{ $petugas->alamat ?: '-' }}</p>
      </div>
      <div class="info-box">
        <span>No. Telepon</span>
        <p>{{ $petugas->no_telepon ?: '-' }}</p>
      </div>
      <div class="info-box">
        <span>Tanggal Bergabung</span>
        <p>{{ $petugas->created_at->format('d F Y') }}</p>
      </div>
    </div>

  </div>

  <div class="card">
    <h3>Ringkasan Petugas</h3>
    <div class="activity-box">
      <div class="activity-left">
        <div class="activity-icon">
          <i class="fa-solid fa-users"></i>
        </div>
        <div>
          <span>Total</span>
          <p>User</p>
        </div>
      </div>
      <div class="activity-count">{{ $userCount }}</div>
    </div>

    <div class="activity-box">
      <div class="activity-left">
        <div class="activity-icon">
          <i class="fa-solid fa-book-open"></i>
        </div>
        <div>
          <span>Total</span>
          <p>Buku Dipinjam</p>
        </div>
      </div>
      <div class="activity-count">{{ $borrowedBooks }}</div>
    </div>

    <div class="activity-box">
      <div class="activity-left">
        <div class="activity-icon">
          <i class="fa-solid fa-rotate-left"></i>
        </div>
        <div>
          <span>Total</span>
          <p>Buku Dikembalikan</p>
        </div>
      </div>
      <div class="activity-count">{{ $returnedBooks }}</div>
    </div>

    <div class="activity-box" style="margin-bottom:0">
      <div class="activity-left">
        <div class="activity-icon">
          <i class="fa-solid fa-clock"></i>
        </div>
        <div>
          <span>Total</span>
          <p>Buku Telat</p>
        </div>
      </div>
      <div class="activity-count">{{ $lateBooks }}</div>
    </div>
  </div>
</div>

<div class="modal" id="editProfileModal" data-auto-open="{{ $errors->any() ? '1' : '0' }}">
  <div class="modal-card">
    <div class="modal-head">
      <h3>Edit Profil Petugas</h3>
      <button class="modal-close" type="button" onclick="closeEditModal()">X</button>
    </div>
    <form action="{{ route('petugas.profile.update') }}" method="POST">
      @csrf
      <div class="form-group">
        <label>Nama</label>
        <input type="text" name="nama" value="{{ old('nama', $petugas->nama) }}" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $petugas->email) }}" required>
      </div>
      <div class="form-group">
        <label>Alamat</label>
        <input type="text" name="alamat" value="{{ old('alamat', $petugas->alamat) }}">
      </div>
      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" name="no_telepon" value="{{ old('no_telepon', $petugas->no_telepon) }}">
      </div>
      <button class="btn-primary" type="submit">Simpan Perubahan</button>
    </form>
  </div>
</div>

<div class="bottom-nav">
  <a href="{{ route('petugas.dashboard') }}">
    <i class="fa-solid fa-house"></i>
    Home
  </a>
  <a href="{{ route('petugas.books.index') }}">
    <i class="fa-solid fa-book"></i>
    Buku
  </a>
  <a href="{{ route('petugas.users.index') }}">
    <i class="fa-solid fa-users"></i>
    User
  </a>
  <a class="active">
    <i class="fa-solid fa-user"></i>
    Profil
  </a>
</div>

<script src="{{ asset('js/petugas-profil.js') }}"></script>

</body>
</html>


