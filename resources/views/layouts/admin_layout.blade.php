<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PUTR CIANJUR</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">

    <style>
        /* Memastikan scroll hanya terjadi di area konten, bukan seluruh halaman */
        body {
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Mencegah scroll pada body */
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100 font-sans text-gray-900">

    <div class="flex h-screen overflow-hidden">
        @include('layouts.sidebar')

        <main class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    @stack('scripts')
</body>
</html>
