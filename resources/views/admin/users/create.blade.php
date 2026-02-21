<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Admin - Tambah User</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/admin-pengguna-tambah.css') }}">
</head>

<body>

<div class="hero">
  <h1>➕ Tambah User</h1>
  <p>Tambah user baru</p>
</div>

<div class="card">
  <h2>Tambah User</h2>

  <form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <div class="form-group">
      <label>Nama</label>
      <input type="text" name="name" required>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>

    <button type="submit">Simpan</button>
  </form>
</div>

</body>
</html>



