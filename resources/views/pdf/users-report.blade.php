<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Pengguna DIGIPUS</title>
    <link rel="stylesheet" href="{{ asset('css/pdf-pengguna-report.css') }}">
</head>
<body>
    <h1>Laporan Data Pengguna DIGIPUS</h1>
    <p>Tanggal: {{ now()->format('d-m-Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="role-badge {{ $user->is_admin ? 'role-admin' : 'role-user' }}">
                        {{ $user->is_admin ? 'Admin' : 'User' }}
                    </span>
                </td>
                <td>{{ $user->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


