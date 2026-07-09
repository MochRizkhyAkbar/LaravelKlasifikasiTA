@extends('layouts.admin_layout')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <style>
        /* CSS untuk memaksa tabel tetap di dalam kontainer */
        .dt-container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1rem !important;
        }

        #tabelPengaduan {
            width: 100% !important;
            table-layout: auto !important;
        }

        .dt-layout-table {
            width: 100% !important;
            overflow-x: auto !important;
            display: block !important;
        }

        /* Styling tambahan agar elemen pencarian terlihat rapi */
        .dt-search input {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 5px 10px !important;
        }
    </style>
@endpush

@section('content')
<div class="w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight">Kelola Pengaduan</h1>
            <p class="text-gray-500 mt-2">Daftar pengaduan yang telah terklasifikasi oleh sistem untuk diverifikasi.</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex justify-end items-center gap-3">
            <form action="{{ route('admin_dinas.kelola') }}" method="GET">
                <select name="bidang" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option value="">Semua Bidang</option>
                    <option value="SDA" {{ request('bidang') == 'SDA' ? 'selected' : '' }}>SDA</option>
                    <option value="Jalan" {{ request('bidang') == 'Jalan' ? 'selected' : '' }}>Jalan</option>
                    <option value="TR" {{ request('bidang') == 'TR' ? 'selected' : '' }}>Tata Ruang</option>
                </select>
            </form>
            <a href="{{ route('admin_dinas.export.pdf', ['bidang' => request('bidang')]) }}"
               class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-200 text-sm">
               Export PDF
            </a>
        </div>

        <!-- Gunakan overflow-hidden agar isi tabel tidak keluar dari rounded-2xl -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table id="tabelPengaduan" class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                    <tr>
                        <th class="px-6 py-4">KODE</th>
                        <th class="px-6 py-4">WAKTU</th>
                        <th class="px-6 py-4">NAMA</th>
                        <th class="px-6 py-4">ADUAN</th>
                        <th class="px-6 py-4">LOKASI</th>
                        <th class="px-6 py-4">KATEGORI</th>
                        <th class="px-6 py-4">CONFIDENCE</th>
                        <th class="px-6 py-4 text-center">STATUS</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pengaduans as $item)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 font-mono text-blue-800">{{ $item->kode_pengaduan }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $item->created_at->format('d/m/y') }}</td>
                        <td class="px-6 py-4 font-medium">{{ $item->nama_pelapor }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ Str::limit($item->isi_pengaduan, 20) }}</td>
                        <td class="px-6 py-4">{{ $item->lokasi }}</td>
                        <td class="px-6 py-4"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">{{ $item->kategori_ai }}</span></td>
                        <td class="px-6 py-4 font-bold text-blue-700">{{ $item->confidence_score }}%</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin_dinas.update', $item->id) }}" method="POST">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="text-xs border-none bg-transparent font-bold {{ $item->status == 'Diterima' ? 'text-green-600' : 'text-yellow-600' }}">
                                    <option value="Pending" {{ $item->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Diterima" {{ $item->status == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="Ditolak" {{ $item->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button type="button" data-pengaduan="{{ json_encode($item) }}" onclick="showDetail(this)"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition">Detail</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white p-8 rounded-2xl w-full max-w-lg shadow-2xl">
        <h2 class="text-2xl font-bold mb-6 text-blue-900">Detail Pengaduan</h2>
        <div id="modalContent" class="space-y-4 text-gray-700"></div>
        <button onclick="document.getElementById('modal').classList.add('hidden')" class="mt-8 w-full bg-blue-900 text-white py-3 rounded-xl font-bold hover:bg-blue-800 transition">Tutup</button>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelPengaduan').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "emptyTable": "Tidak ada data yang tersedia",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data"
            }
        });
    });

    function showDetail(button) {
        const item = JSON.parse(button.getAttribute('data-pengaduan'));
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('modalContent').innerHTML = `
            <div class="grid grid-cols-2 gap-4 text-sm">
                <p><strong>Kode:</strong> ${item.kode_pengaduan}</p>
                <p><strong>Nama:</strong> ${item.nama_pelapor}</p>
            </div>
            <p><strong>No WA:</strong> ${item.no_wa}</p>
            <p><strong>Lokasi:</strong> ${item.lokasi}</p>
            <p><strong>Isi:</strong> ${item.isi_pengaduan}</p>
            <img src="/storage/${item.foto_bukti}" class="w-full h-56 object-cover rounded-xl mt-4 border border-gray-100" alt="Foto">
        `;
    }
</script>
@endpush
@endsection
