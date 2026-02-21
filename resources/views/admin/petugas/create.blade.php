<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Admin - Tambah Petugas</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-petugas-tambah.css') }}">
</head>

<body>

<header class="header">
  <div class="brand">
    <img src="https://via.placeholder.com/36">
    DIGIPUS
  </div>
  <div class="header-right">
    @include('components.notification-bell')
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout">Logout</button>
    </form>
  </div>
</header>

<div class="hero">
  <h1>➕ Tambah Petugas</h1>
  <p>Tambah petugas baru</p>
</div>

<div class="main-content">
  <div class="form-card">
    @if ($errors->any())
        <div class="alert error" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.petugas.store') }}" method="POST">
      @csrf
      <div class="form-group">
        <label>Nama</label>
        <input type="text" name="nama" value="{{ old('nama') }}" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <div class="form-group">
        <label>Alamat</label>
        <input type="text" name="alamat" value="{{ old('alamat') }}">
      </div>

      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" name="no_telepon" value="{{ old('no_telepon') }}">
      </div>

      <button type="submit" class="submit-btn"><i class="fa-solid fa-plus"></i> Tambah Petugas</button>
    </form>
  </div>
</div>

</body>
</html>




