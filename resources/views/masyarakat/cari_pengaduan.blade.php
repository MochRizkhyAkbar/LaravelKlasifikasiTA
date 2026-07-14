<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Pengaduan - PUTR Cianjur</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="sticky top-0 z-50 flex justify-between items-center p-6 bg-white shadow-sm border-b-2 border-blue-900">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/Logo PUTR.png') }}" alt="Logo PUTR" class="h-12 w-auto">
        </a>
        <div class="flex gap-6 font-semibold text-blue-900">
            <a href="{{ route('home') }}" class="hover:text-blue-700">Home</a>
            <a href="{{ route('pengaduan.create') }}" class="hover:text-blue-700">Buat Pengaduan</a>
            <a href="{{ route('pengaduan.search') }}" class="border-b-2 border-blue-900">Cari Aduan</a>
        </div>
    </nav>

    <main class="max-w-lg mx-auto py-16 px-6">
        <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-blue-900">
            <h2 class="text-2xl font-bold mb-6 text-blue-900">Cari Status Pengaduan</h2>

            <form action="{{ route('pengaduan.search') }}" method="GET" class="mb-8">
                <div class="flex gap-2">
                    <input type="text" name="kode" value="{{ request('kode') }}"
                        class="flex-1 border rounded-lg p-3 focus:ring-2 focus:ring-blue-900 focus:outline-none"
                        placeholder="Masukkan Kode PGD-..." required>
                    <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition font-bold">
                        Cari
                    </button>
                </div>
            </form>

            @if(session('error'))
                <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @if(isset($pengaduan))
                <div class="border-t pt-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Kode Pengaduan:</p>
                        <p class="font-mono font-bold text-lg text-blue-900">{{ $pengaduan->kode_pengaduan }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 font-medium">Status:</p>
                        <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold mt-1
                            {{ $pengaduan->status == 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                               ($pengaduan->status == 'Diterima' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                            {{ $pengaduan->status }}
                        </span>
                    </div>

                    @if($pengaduan->status == 'Ditolak' && !empty($pengaduan->alasan_penolakan))
                        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200">
                            <strong>Alasan Penolakan:</strong>
                            <p class="mt-1">{{ $pengaduan->alasan_penolakan }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </main>

</body>
</html>
