<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login DigiPus</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/masuk-masuk.css') }}">
</head>

<body>

<div class="hero">
  <div class="overlay"></div>

  <div class="content">

    <div class="branding">
      <h1>DigiPus</h1>
      <p>Membuka jendela dunia melalui literasi digital</p>
    </div>

    <form class="login-box" method="POST" action="{{ route('login.process') }}">
      @csrf
      <h3>Masuk</h3>
      <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
      <input type="password" name="password" placeholder="Password" required>
      <button>Login</button>
      <span>Belum punya akun? <a href="{{ route('register') }}">Daftar</a></span>
    </form>

  </div>
</div>

<script src="{{ asset('js/login.js') }}"></script>
<div id="loginErrorToast" class="login-toast" role="alert" aria-live="polite" data-login-error="{{ session('login_error') }}"></div>
<script src="{{ asset('js/eror-login.js') }}"></script>
</body>
</html>


