<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DIGIPUS Admin - Data Kategori</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-kategori-daftar.css') }}">
</head>

<body>

<div class="topbar">
  <div class="brand">
    <img src="{{ asset('images/logo digipus.png') }}" alt="Logo DIGIPUS">
    <h2>DIGIPUS</h2>
  </div>
  <div class="topbar-right">
    @include('components.notification-bell')
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout-btn">LOG OUT</button>
    </form>
  </div>
</div>

<div class="hero">
  <h1>Data Kategori Buku</h1>
  <p>Kelola kategori buku dengan rapi</p>
</div>

@if(session('success'))
<div class="alert-success auto-dismiss-alert">
  {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="alert-error auto-dismiss-alert">
  {{ $errors->first() }}
</div>
@endif

<div class="main-content">

  <div class="shortcut-cards">
    <a href="{{ route('admin.books.index') }}" class="card">
      <i class="fa-solid fa-book"></i>
      <h2>Buku</h2>
      <p>CRUD buku (lihat, tambah, edit, hapus)</p>
    </a>
    <a href="{{ route('admin.kategori.index') }}" class="card">
      <i class="fa-solid fa-tags"></i>
      <h2>Kategori</h2>
      <p>Kelola kategori buku</p>
    </a>
    <a href="{{ route('admin.books.pending') }}" class="card">
      <i class="fa-solid fa-file-signature"></i>
      <h2>Konfirmasi</h2>
      <p>Konfirmasi buku yang di-upload petugas</p>
    </a>
  </div>

  <button type="button" class="btn" onclick="openAddModal()">
    <i class="fa-solid fa-plus"></i> Tambah Kategori
  </button>

  <div class="table-container">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Kategori</th>
          <th>Jumlah Buku</th>
          <th class="col-action">Aksi</th>
        </tr>
      </thead>
      <tbody>
      @forelse($kategoris as $kategori)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $kategori->name }}</td>
          <td>{{ $kategori->books_count }} buku</td>
          <td class="col-action">
            <div class="action-buttons">
              <button
                type="button"
                class="action-btn edit"
                onclick="openEditModal({{ $kategori->id }}, '{{ e($kategori->name) }}')">
                <i class="fa-solid fa-pen"></i>
              </button>
              <form action="{{ route('admin.kategori.destroy', $kategori->id) }}" method="POST"
                    data-popup-confirm
                    data-popup-title="Konfirmasi Hapus"
                    data-popup-message="Hapus kategori ini?">
                @csrf
                @method('DELETE')
                <button class="action-btn delete">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" style="text-align:center;padding:40px;color:#666;">
            Belum ada kategori
          </td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

</div>

<div class="modal" id="addKategoriModal">
  <div class="modal-card">
    <div class="modal-head">
      <h3>Tambah Kategori</h3>
      <button type="button" class="modal-close" onclick="closeAddModal()">X</button>
    </div>
    <form action="{{ route('admin.kategori.store') }}" method="POST">
      @csrf
      <input type="hidden" name="_modal" value="add">
      <div class="form-group">
        <label for="add-kategori-name">Nama Kategori</label>
        <input id="add-kategori-name" type="text" name="name" value="{{ old('name') }}" required>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-secondary" onclick="closeAddModal()">Batal</button>
        <button type="submit" class="btn">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal" id="editKategoriModal">
  <div class="modal-card">
    <div class="modal-head">
      <h3>Edit Kategori</h3>
      <button type="button" class="modal-close" onclick="closeEditModal()">X</button>
    </div>
    <form id="editKategoriForm" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" name="_modal" value="edit">
      <input type="hidden" name="kategori_id" id="edit-kategori-id">
      <div class="form-group">
        <label for="edit-kategori-name">Nama Kategori</label>
        <input id="edit-kategori-name" type="text" name="name" required>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
        <button type="submit" class="btn">Update</button>
      </div>
    </form>
  </div>
</div>

@include('admin.navbar')
<div
  id="kategoriKonfigurasi"
  data-base-url="{{ url('/admin/kategori') }}"
  data-modal-awal="{{ $errors->any() ? (old('_modal') === 'edit' && old('kategori_id') ? 'edit' : 'tambah') : '' }}"
  data-kategori-id="{{ old('kategori_id') }}"
  data-kategori-nama="{{ old('name') }}"
></div>

<script src="{{ asset('js/admin-kategori-daftar.js') }}"></script>

</body>
</html>









