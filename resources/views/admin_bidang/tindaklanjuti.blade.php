@extends('layouts.admin_layout')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-1">Formulir Menindaklanjuti Pengaduan</h1>
    <p class="text-gray-600 mb-6 font-bold text-blue-800">
        Menangani Pengaduan Bidang: {{ str_replace('bidang', '', Auth::user()->getRoleNames()->first()) }}
    </p>

    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <table id="tabelTindakLanjut" class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Aduan</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Foto</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengaduans as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-medium">{{ $item->user ? $item->user->name : ($item->nama_pelapor ?? 'Anonim') }}</td> <td class="px-4 py-3">{{ Str::limit($item->isi_pengaduan, 30) }}</td>
                    <td class="px-4 py-3">{{ $item->lokasi }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">
                            {{ str_replace('bidang', '', $item->kategori_ai) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($item->foto_bukti)
                            <a href="{{ asset('storage/'.$item->foto_bukti) }}" target="_blank" class="text-blue-600 underline">IMG</a>
                        @else - @endif
                    </td>
                    <td class="px-4 py-3 font-semibold">{{ $item->status }}</td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="openModal({{ json_encode($item) }})" class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-bold">Edit</button>
                        <button onclick="openDetailModal({{ json_encode($item) }})" class="bg-gray-500 text-white px-3 py-1 rounded text-sm font-bold ml-2">Detail</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="actionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Proses Pengaduan</h2>
        <form id="actionForm" method="POST">
            @csrf @method('PUT')
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" id="statusSelect" class="w-full border p-2 rounded mb-3">
                <option value="Diterima">Terima</option>
                <option value="Diproses">Proses</option>
                <option value="Dikembalikan">Kembalikan</option>
                <option value="Selesai">Selesai</option> </select>
            </select>
            <label class="block text-sm font-medium mb-1">Alihkan Kategori</label>
            <select name="kategori_baru" id="kategoriSelect" class="w-full border p-2 rounded mb-3">
                <option value="">-- Tetap --</option>
                <option value="bidangSDA">SDA</option>
                <option value="bidangJalan">Jalan</option>
                <option value="bidangTATARUANG">Tata Ruang</option>
                <option value="bidangBINKON">Bina Konstruksi</option>
            </select>
            <label class="block text-sm font-medium mb-1">Catatan</label>
            <textarea name="catatan" id="catatanText" class="w-full border p-2 rounded mb-4" rows="3"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('actionModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                <button type="submit" id="btnSimpan" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-2xl">
        <h2 class="text-xl font-bold mb-4">Detail Lengkap</h2>
        <div class="space-y-2 text-gray-700">
            <p><strong>Kode:</strong> <span id="detKode"></span></p>
            <p><strong>Nama:</strong> <span id="detNama"></span></p>
            <p><strong>Lokasi:</strong> <span id="detLokasi"></span></p>
            <p><strong>Isi:</strong> <span id="detIsi"></span></p>
            <div class="mt-4">
                <img id="detFoto" src="" alt="Bukti" class="w-full rounded-lg border">
            </div>
        </div>
        <button onclick="document.getElementById('detailModal').classList.add('hidden')" class="w-full mt-6 py-2 bg-blue-900 text-white rounded-lg font-bold">Tutup</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelTindakLanjut').DataTable({
            "order": [] // Mencegah DataTables mengurutkan ulang secara otomatis
        });
    });

    function openModal(item) {
        document.getElementById('actionForm').action = `/admin-bidang/update/${item.id}`;
        $('#statusSelect').val(item.status);
        $('#catatanText').val(item.catatan_bidang || '');
        document.getElementById('actionModal').classList.remove('hidden');
    }

    function openDetailModal(item) {
        const nama = item.user ? item.user.name : (item.nama_pelapor || 'Anonim');
        document.getElementById('detKode').innerText = item.kode_pengaduan || 'N/A';
        document.getElementById('detNama').innerText = nama;
        document.getElementById('detLokasi').innerText = item.lokasi;
        document.getElementById('detIsi').innerText = item.isi_pengaduan;
        document.getElementById('detFoto').src = item.foto_bukti ? '/storage/' + item.foto_bukti : '';
        document.getElementById('detailModal').classList.remove('hidden');
    }
</script>
@endpush
