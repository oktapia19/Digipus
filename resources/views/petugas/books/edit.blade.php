<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Buku - DIGIPUS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/petugas-buku-ubah.css') }}">
</head>

<body>

<!-- HEADER -->
<div class="header">
  <div class="header-left">
    <img src="{{ asset('images/logo digipus.png') }}" alt="Logo DIGIPUS">
    <h2>DIGIPUS</h2>
  </div>
  <div class="header-right">
    @include('components.notification-bell')
    <a href="{{ route('petugas.books.index') }}" class="header-action">KEMBALI</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="header-action">LOG OUT</button>
    </form>
  </div>
</div>

<!-- CONTENT -->
<div class="container">
<div class="card">

<!-- FOTO -->
<div class="photo-wrapper">
  <img id="previewImage"
    src="{{ $book->cover ? asset('storage/'.$book->cover) : 'https://images.unsplash.com/photo-1512820790803-83ca734da794' }}">
  <div class="photo-overlay">
    {{ $book->judul }}
  </div>
</div>

<!-- FORM -->
<form action="{{ route('petugas.books.update',$book->id) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')
@if ($errors->any())
  <div class="alert error" role="alert" style="margin-bottom:12px;">
    {{ $errors->first() }}
  </div>
@endif

<div class="form-group">
  <label>Judul Buku *</label>
  <input type="text" name="judul" required value="{{ $book->judul }}">
</div>

<div class="form-group">
  <label>ISBN (8 digit) *</label>
  <input
    type="text"
    name="isbn"
    value="{{ old('isbn', $book->isbn) }}"
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
    <input type="text" name="penulis" value="{{ $book->penulis }}">
  </div>
  <div class="form-group">
    <label>Penerbit</label>
    <input type="text" name="penerbit" value="{{ $book->penerbit }}">
  </div>
</div>

<div class="row">
  <div class="form-group">
    <label>Tahun</label>
    <input type="number" name="tahun" min="1900" max="2026" value="{{ $book->tahun }}">
  </div>
  <div class="form-group">
    <label>Stok *</label>
    <input type="number" name="stok" min="0" required value="{{ $book->stok }}">
  </div>
</div>

<div class="form-group">
  <label>Sinopsis</label>
  <textarea name="sinopsis">{{ $book->sinopsis }}</textarea>
</div>

<div class="form-group">
  <div class="kategori-box">
    <div class="kategori-header">Kategori</div>
    <div class="kategori-list">
      @foreach($kategori as $kat)
      <label class="kategori-item">
        <input type="checkbox" name="kategori[]" value="{{ $kat->id }}"
          {{ $book->kategoris->contains($kat->id) ? 'checked' : '' }}>
        {{ $kat->name }}
      </label>
      @endforeach
    </div>
  </div>
</div>

<div class="form-group file-upload">
  <label class="upload-btn">
    <i class="fa-solid fa-cloud-arrow-up"></i>
    Ganti Cover
    <input type="file" name="cover" id="coverInput" accept="image/*">
  </label>
</div>

<button class="btn">Simpan Perubahan</button>
</form>

</div>
</div>

<script src="{{ asset('js/petugas-buku-ubah.js') }}"></script>


</body>
</html>



