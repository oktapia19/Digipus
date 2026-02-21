<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar DigiPus</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

<div class="hero">
  <div class="overlay"></div>

  <div class="content">

    <!-- BRANDING (SAMA) -->
    <div class="branding">
      <h1>DigiPus</h1>
      <p>Membuka jendela dunia melalui literasi digital</p>
    </div>

    <!-- REGISTER BOX (SAMA CLASS & STYLE) -->
    <form class="login-box" method="POST" action="{{ route('register.process') }}">
      @csrf

      <h3>Daftar</h3>

      <input type="text" name="name" placeholder="Nama Lengkap" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>

      <button>Daftar</button>

      <span>
        Sudah punya akun?
        <a href="{{ route('login') }}">Masuk</a>
      </span>
    </form>

  </div>
</div>

</body>
</html>
