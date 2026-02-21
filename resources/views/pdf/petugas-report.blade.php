<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Petugas DIGIPUS</title>
    <link rel="stylesheet" href="{{ asset('css/pdf-petugas-report.css') }}">
</head>
<body>
    <h1>Laporan Data Petugas DIGIPUS</h1>
    <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($petugas as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->alamat ?? '-' }}</td>
                <td>{{ $p->no_telepon ?? '-' }}</td>
                <td>{{ $p->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


