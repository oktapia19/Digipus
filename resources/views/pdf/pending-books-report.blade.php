<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Menunggu Konfirmasi DIGIPUS</title>
    <link rel="stylesheet" href="{{ asset('css/pdf-tertunda-buku-report.css') }}">
</head>
<body>
    <h1>Laporan Buku Menunggu Konfirmasi DIGIPUS</h1>
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
                <th>Status</th>
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
                @php
                    $statusLabels = [
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'menunggu_hapus' => 'Menunggu Hapus',
                    ];
                @endphp
                <td>{{ $statusLabels[$book->status] ?? ucfirst($book->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


