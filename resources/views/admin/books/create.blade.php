<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Buku - DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin-buku-tambah.css') }}">
</head>

<body>

<div class="header">
  <div class="header-left">
    <h2>DIGIPUS</h2>
  </div>
  <a href="{{ route('admin.books.index') }}" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i> Kembali
  </a>
</div>

<div class="hero">
  <h1>➕ Tambah Buku</h1>
  <p>Tambah buku baru ke koleksi</p>
</div>

<div class="container">
<div class="card">

<!-- COVER -->
<div class="photo-wrapper">
  <img id="previewImage">
  <div class="photo-placeholder" id="placeholder">
    <i class="fa-regular fa-image"></i>
    <div>Belum ada cover</div>
  </div>
</div>

<!-- FORM -->
<form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
@csrf
@if ($errors->any())
  <div class="alert error" role="alert" style="margin-bottom:12px;">
    {{ $errors->first() }}
  </div>
@endif

<div class="form-group">
  <label>Judul *</label>
  <input type="text" name="judul" required>
</div>

<div class="form-group">
  <label>ISBN (8 digit) *</label>
  <input
    type="text"
    name="isbn"
    value="{{ old('isbn') }}"
    required
    inputmode="numeric"
    pattern="[0-9]{8}"
    minlength="8"
    maxlength="8"
    placeholder="Contoh: 12345678">
</div>

<div class="row">
  <div class="form-group">
    <label>Penulis</label>
    <input type="text" name="penulis">
  </div>
  <div class="form-group">
    <label>Penerbit</label>
    <input type="text" name="penerbit">
  </div>
</div>

<div class="row">
  <div class="form-group">
    <label>Tahun</label>
    <input type="number" name="tahun">
  </div>
  <div class="form-group">
    <label>Stok *</label>
    <input type="number" name="stok" required>
  </div>
</div>

<div class="form-group">
  <label>Sinopsis</label>
  <textarea name="sinopsis"></textarea>
</div>

<div class="form-group">
  <label>Kategori</label>
  <div class="multi-wrapper">
    <div id="multiSelect">
      <span class="placeholder">Pilih kategori</span>
    </div>
    <div class="options-list" id="optionsList">
      @foreach($kategori as $kat)
        <div class="option" data-id="{{ $kat->id }}">{{ $kat->name }}</div>
      @endforeach
    </div>
  </div>
  <div id="kategoriInputs"></div>
</div>

<div class="form-group">
  <label>Cover Buku</label>
  <label class="file-upload" for="coverInput">
    <i class="fa-solid fa-cloud-arrow-up"></i> Pilih gambar cover
  </label>
  <input type="file" name="cover" id="coverInput" accept="image/*">
</div>

<button class="btn">Simpan Buku</button>
</form>

</div>
</div>

<script src="{{ asset('js/admin-buku-tambah.js') }}"></script>

</body>
</html>



