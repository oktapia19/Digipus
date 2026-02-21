<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Admin - Edit User</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-pengguna-ubah.css') }}">
</head>

<body>

<header class="header">
  <div class="brand">
    <img src="https://via.placeholder.com/36">
    DIGIPUS
  </div>
  <a href="{{ route('admin.users.index') }}" class="back-btn">KEMBALI</a>
</header>

<div class="hero">
  <h1>✏️ Edit User</h1>
  <p>Edit data user dengan rapi</p>
</div>

<div class="container">
  <div class="card">
    <h2>Edit Data Pengguna</h2>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <!-- FOTO -->
      <div class="photo-box">
        <img src="{{ $user->photo ? asset('storage/'.$user->photo) : 'https://via.placeholder.com/90' }}">
        <label class="upload-btn">
          Upload Foto
          <small>PNG / JPG, max 2MB</small>
          <input type="file" name="photo" hidden>
        </label>
      </div>

      <!-- FORM -->
      <div class="form-grid">
        <div class="form-group">
          <label>Nama Pengguna</label>
          <input type="text" name="name" value="{{ old('name',$user->name) }}" required>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email',$user->email) }}" required>
        </div>

        <div class="form-group">
          <label>Alamat</label>
          <input type="text" name="address" value="{{ old('address',$user->address) }}">
        </div>

        <div class="form-group">
          <label>No Handphone</label>
          <input type="text" name="phone" value="{{ old('phone',$user->phone) }}">
        </div>

        <div class="form-group">
          <label>Kata Sandi (opsional)</label>
          <input type="password" name="password" placeholder="Kosongkan jika tidak diubah">
        </div>
      </div>

      <!-- BUTTON -->
      <div class="actions">
        <a href="{{ route('admin.users.index') }}" class="btn-cancel">Batal</a>
        <button class="btn-save">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

@include('admin.navbar')

</body>
</html>



