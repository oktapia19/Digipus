<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Form Peminjaman</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/peminjaman-tambah.css') }}">
</head>
<body>

<div class="wrap">
  <div class="left">
    <h2>FORM PEMINJAMAN</h2>
    <p>Isi data dengan lengkap untuk meminjam buku. Pastikan informasi valid agar proses peminjaman berjalan lancar.</p>
    <div class="meta">Batas denda keterlambatan Rp 5.000 per hari</div>
  </div>
  <div class="right">
    <div class="title">
      <div>
        <h3>Ajukan Peminjaman</h3>
        <p>Lengkapi form berikut untuk memproses permintaan peminjaman buku.</p>
      </div>
      <div class="badge-book">{{ $book->judul }}</div>
    </div>

    @if($errors->any())
      <div class="error-list">
        Data belum valid. Periksa bagian berikut:
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('peminjaman.store', $book->id) }}">
      @csrf

      <div class="form-grid">
        <div class="section">
          <h4>Data Peminjam</h4>
          <label>Nama Lengkap</label>
          <input class="@error('nama_lengkap') is-invalid @enderror" type="text" name="nama_lengkap" value="{{ old('nama_lengkap', auth()->user()->nama_lengkap) }}" placeholder="Contoh: Budi Santoso">
          @error('nama_lengkap')<div class="field-error">{{ $message }}</div>@enderror

          <label>Nomor Telepon</label>
          <input class="@error('no_telepon') is-invalid @enderror" type="text" name="no_telepon" value="{{ old('no_telepon', auth()->user()->no_telepon) }}" placeholder="08xxxxxxxxxx">
          @error('no_telepon')<div class="field-error">{{ $message }}</div>@enderror

          <label>Email</label>
          <input type="email" value="{{ auth()->user()->email }}" class="readonly" disabled>
        </div>

        <div class="section">
          <h4>Detail Peminjaman</h4>
          <div class="row">
            <div>
              <label>Durasi Peminjaman <span class="required">*</span></label>
              <input class="@error('durasi') is-invalid @enderror" type="number" name="durasi" id="durasi" min="1" max="12" value="{{ old('durasi', 7) }}">
              @error('durasi')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div>
              <label>Satuan <span class="required">*</span></label>
              <select class="@error('durasi_satuan') is-invalid @enderror" name="durasi_satuan" id="durasi_satuan">
                <option value="hari" {{ old('durasi_satuan', 'hari') === 'hari' ? 'selected' : '' }}>Hari (maks. 12)</option>
                <option value="jam" {{ old('durasi_satuan') === 'jam' ? 'selected' : '' }}>Jam (maks. 24)</option>
              </select>
              @error('durasi_satuan')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div>
              <label>Tanggal Jatuh Tempo</label>
              <input type="date" id="tanggal_kembali" class="readonly" readonly>
            </div>
          </div>
          <label>Alamat Rumah</label>
          <textarea class="@error('alamat') is-invalid @enderror" name="alamat" placeholder="Masukkan alamat lengkap">{{ old('alamat', auth()->user()->alamat) }}</textarea>
          @error('alamat')<div class="field-error">{{ $message }}</div>@enderror
          <div class="hint">Jika terlambat, denda berlaku <b>Rp 5.000</b> per hari keterlambatan.</div>
        </div>
      </div>

      <label class="agree">
        <input type="checkbox" name="agree" value="1" {{ old('agree') ? 'checked' : '' }} required>
        <span>Saya menyetujui syarat dan ketentuan peminjaman buku.</span>
      </label>
      @error('agree')<div class="field-error">{{ $message }}</div>@enderror

      <div class="actions">
        <a href="{{ route('books.show', $book->id) }}" class="btn btn-muted">Batal</a>
        <button class="btn btn-primary">Ajukan Peminjaman</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/peminjaman-tambah.js') }}"></script>

</body>
</html>


