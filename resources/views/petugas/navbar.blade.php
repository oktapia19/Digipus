<link rel="stylesheet" href="{{ asset('css/petugas-bilahnavigasi.css') }}">

<div class="petugas-navbar">

  <a href="{{ route('petugas.dashboard') }}"
   class="{{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
   <i class="fa-solid fa-house"></i>
   Home
</a>

<a href="{{ route('petugas.books.index') }}"
   class="{{ request()->routeIs('petugas.books.*') ? 'active' : '' }}">
   <i class="fa-solid fa-book"></i>
   Buku
</a>

  <a href="{{ route('petugas.users.index') }}"
   class="{{ request()->routeIs('petugas.users.*') ? 'active' : '' }}">
   <i class="fa-solid fa-users"></i>
   User
  </a>



  <!-- PROFIL -->
  <a href="{{ route('petugas.profile') }}"
   class="{{ request()->routeIs('petugas.profile') ? 'active' : '' }}">
    <i class="fa-solid fa-user"></i>
    Profil
  </a>

</div>


