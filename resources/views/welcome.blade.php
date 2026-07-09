<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengaduan PUTR Cianjur</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar Sticky -->
    <nav class="sticky top-0 z-50 flex justify-between items-center p-6 bg-white shadow-sm border-b-2 border-blue-900">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/Logo PUTR.png') }}" alt="Logo PUTR" class="h-12 w-auto">
        </a>
        <div class="flex gap-6 font-semibold text-blue-900">
            <a href="{{ route('home') }}" class="border-b-2 border-blue-900">Home</a>
            <a href="{{ route('pengaduan.create') }}" class="hover:text-blue-700">Buat Pengaduan</a>
            <a href="{{ route('pengaduan.search') }}" class="hover:text-blue-700">Cari Aduan</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="text-center py-20 px-6">
        <h1 class="text-5xl font-extrabold text-blue-900 mb-6">Sampaikan Keluhan Infrastruktur Anda Secara Langsung</h1>
        <p class="text-lg text-gray-600 mb-10 max-w-2xl mx-auto">Sistem Informasi Pengaduan Dinas Pekerjaan Umum dan Tata Ruang Kabupaten Cianjur</p>
        <a href="{{ route('pengaduan.create') }}" class="inline-block bg-blue-900 text-white px-10 py-4 rounded-lg hover:bg-blue-800 font-bold shadow-lg transition-transform hover:scale-105">
            Buat Pengaduan Sekarang
        </a>
    </main>

    <!-- Garis Pemisah yang Elegan -->
    <div class="max-w-4xl mx-auto px-6">
        <div class="h-px bg-gradient-to-r from-transparent via-blue-900 to-transparent w-full"></div>
    </div>

    <!-- Info Section: Tutorial & Bidang -->
    <section class="max-w-6xl mx-auto py-16 px-6 grid md:grid-cols-2 gap-12">
        <!-- Tutorial -->
        <div>
            <h2 class="text-2xl font-bold text-blue-900 mb-6">Cara Melakukan Pengaduan</h2>
            <ol class="space-y-4 text-gray-700 list-decimal list-inside">
                <li>Klik tombol <strong>"Buat Pengaduan"</strong> </li>
                <li>Isi formulir pengaduan dengan data diri dan detail kerusakan</li>
                <li>Unggah foto bukti fisik kerusakan infrastruktur</li>
                <li>Klik kirim dan simpan <strong>Kode Pengaduan</strong> yang muncul</li>
                <li>Gunakan kode tersebut di menu <strong>"Cari Aduan"</strong> untuk melacak status</li>
            </ol>
        </div>

        <!-- Bidang -->
        <div>
            <h2 class="text-2xl font-bold text-blue-900 mb-6">Bidang di Dinas PUTR</h2>
            <ul class="grid grid-cols-2 gap-3 text-sm">
                <li class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-900 font-semibold">Bidang Sumber Daya Air</li>
                <li class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-900 font-semibold">Preservasi Jalan</li>
                <li class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-900 font-semibold">Pembangunan Jalan</li>
                <li class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-900 font-semibold">Tata Ruang</li>
                <li class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-900 font-semibold">Bina Kontruksi & Teknik</li>
                <li class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-900 font-semibold">Sekretariat</li>
            </ul>
        </div>
    </section>

    <!-- Footer & Alamat (Klik untuk Maps) -->
    <footer class="bg-blue-900 text-white py-12 px-6 mt-10">
        <div class="max-w-4xl mx-auto text-center">
            <h3 class="font-bold text-lg mb-2">Alamat Dinas PUTR Kabupaten Cianjur</h3>
            <!-- Link Google Maps -->
            <a href="https://www.google.com/maps/search/Dinas+PUTR+Kabupaten+Cianjur" target="_blank" class="text-blue-100 hover:text-white underline transition">
                Jl. Adi Sucipta, Pamoyanan, Kec. Cianjur, Kabupaten Cianjur, Jawa Barat 43212
            </a>
            <p class="text-sm text-blue-300 mt-8">&copy; {{ date('Y') }} Dinas PUTR Kabupaten Cianjur |
                <a href="{{ route('login') }}" class="hover:underline text-white">Login Admin</a>
            </p>
        </div>
    </footer>

</body>
</html>
