<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Buku DIGIPUS</title>
    <link rel="stylesheet" href="{{ asset('css/pdf-buku-report.css') }}">
</head>
<body>
    <h1>Laporan Data Buku DIGIPUS</h1>
    <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>Kategori</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $book->judul }}</td>
                <td>{{ $book->penulis ?? '-' }}</td>
                <td>{{ $book->penerbit ?? '-' }}</td>
                <td>{{ $book->tahun ?? '-' }}</td>
                <td>
                    <div class="kategori">
                        @foreach($book->kategoris as $kat)
                            <span class="badge">{{ $kat->name }}</span>
                        @endforeach
                    </div>
                </td>
                <td>{{ $book->stok }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


