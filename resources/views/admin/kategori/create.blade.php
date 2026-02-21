<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Admin - Tambah Kategori</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-kategori-tambah.css') }}">
</head>

<body>
<!-- TOP BAR -->
<div class="topbar">
  <div class="brand">
    <img src="https://via.placeholder.com/34"> DIGIPUS
  </div>
  <div class="topbar-right">
    @include('components.notification-bell')
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout-btn">LOG OUT</button>
    </form>
  </div>
</div>

<!-- HERO -->
<div class="hero">
  <h1>➕ Tambah Kategori Buku</h1>
  <p>Tambah kategori buku baru</p>
</div>

@if(session('success'))
<div class="alert-success auto-dismiss-alert">
  {{ session('success') }}
</div>
@endif

<div class="main-content">
  <div class="form-container">
    <form method="POST" action="{{ route('admin.kategori.store') }}">
      @csrf
      <div class="form-group">
        <label for="name">Nama Kategori</label>
        <input type="text" id="name" name="name" required>
      </div>
      <button type="submit" class="btn">
        <i class="fa-solid fa-save"></i> Simpan
      </button>
      <a href="{{ route('admin.kategori.index') }}" class="btn" style="background:#6B7280;margin-left:10px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </a>
    </form>
  </div>
</div>

@include('admin.navbar')
<script src="{{ asset('js/admin-kategori-tambah.js') }}"></script>
</body>
</html>



