<!-- DIGIPUS NAV + HEADER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/admin-bilahnavigasi.css') }}">

<!-- ================= BOTTOM NAV ================= -->
<div class="bottom-nav">

  <!-- DASHBOARD -->
  <a href="{{ route('admin.dashboard') }}"
     class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-house"></i>
    Home
  </a>

  <!-- BUKU -->
  <a href="{{ route('admin.books.index') }}"
     class="{{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
    <i class="fa-solid fa-book"></i>
    Buku
  </a>

  <!-- USER -->
  <a href="{{ route('admin.users.index') }}"
     class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="fa-solid fa-users"></i>
    User
  </a>

  <!-- PETUGAS -->
  <a href="{{ route('admin.petugas.index') }}"
     class="{{ request()->routeIs('admin.petugas.*') ? 'active' : '' }}">
    <i class="fa-solid fa-user-tie"></i>
    Petugas
  </a>

  <!-- PROFIL -->
  <a href="{{ route('admin.profile') }}"
     class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
    <i class="fa-solid fa-user"></i>
    Profil
  </a>

</div>


