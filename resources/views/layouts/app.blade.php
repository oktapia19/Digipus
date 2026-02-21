<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>@yield('title','DIGIPUS')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/tataletak-aplikasi.css') }}">

@stack('style')
</head>

<body>

{{-- NAVBAR BAWAH (ROLE BASED) --}}
@auth
    @includeIf('user.navbar')
@endauth

{{-- CONTENT --}}
@yield('content')

@stack('script')
</body>
</html>


