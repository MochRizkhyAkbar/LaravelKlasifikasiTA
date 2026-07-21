@extends('layouts.admin_layout')

@section('content')
<div class="w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight">Kelola Pengaduan</h1>
            <p class="text-gray-500 mt-2">Daftar pengaduan yang telah terklasifikasi oleh sistem untuk diverifikasi.</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm mb-6 flex justify-between items-center gap-3">
            <div class="text-sm text-gray-600 font-medium">
                Menampilkan
                <span class="mx-1 px-3 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-full font-bold shadow-sm">
                    {{ $pengaduans->count() }}
                </span>
                data
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('admin_dinas.kelola') }}" method="GET">
                    <select name="bidang" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5">
                        <option value="">Semua Bidang</option>
                        <option value="bidangBINKON" {{ request('bidang') == 'bidangBINKON' ? 'selected' : '' }}>BINKON</option>
                        <option value="bidangSDA" {{ request('bidang') == 'bidangSDA' ? 'selected' : '' }}>SDA</option>
                        <option value="bidangJALAN" {{ request('bidang') == 'bidangJALAN' ? 'selected' : '' }}>Jalan</option>
                        <option value="bidangTATARUANG" {{ request('bidang') == 'bidangTATARUANG' ? 'selected' : '' }}>Tata Ruang</option>
                    </select>
                </form>

                <a href="{{ route('admin_dinas.export.pdf', ['bidang' => request('bidang')]) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm">
                    Export PDF
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
            <table id="tabelPengaduan" class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                    <tr>
                        <th class="px-6 py-4">KODE</th>
                        <th class="px-6 py-4">WAKTU</th>
                        <th class="px-6 py-4">NAMA</th>
                        <th class="px-6 py-4">ADUAN</th>
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
                        <td class="px-6 py-4 text-xs font-semibold text-blue-800">{{ $item->kategori_ai ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs font-bold text-blue-700">{{ $item->confidence_score ?? '0' }}%</td>
                        <td class="px-6 py-4 font-bold text-center">
                            @if($item->status == 'Pending')
                                <span class="text-yellow-600">Menunggu Verifikasi</span>
                            @elseif($item->status == 'Diterima')
                                <span class="text-blue-600">Diteruskan ke {{ str_replace('bidang', '', $item->kategori_ai) }}</span>
                            @elseif($item->status == 'Didisposisikan')
                                <span class="text-orange-600">Dialihkan ke {{ str_replace('bidang', '', $item->kategori_ai) }}</span>
                            @elseif($item->status == 'Dikembalikan')
                                <span class="text-red-600 cursor-help" title="{{ $item->catatan_bidang }}">Dikembalikan (Perlu Review)</span>
                            @elseif($item->status == 'Diproses')
                                <span class="text-purple-600">Sedang Diproses Bidang</span>
                            @else
                                <span class="text-green-600">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openVerificationModal({{ $item->id }})" class="bg-blue-500 text-white text-xs px-3 py-1 rounded font-bold hover:bg-blue-700">Edit</button>
                            <button type="button" data-pengaduan="{{ json_encode($item) }}" onclick="showDetail(this)" class="bg-gray-500 text-white text-xs px-3 py-1 rounded font-bold hover:bg-orange-700 ml-1 transition duration-200">Detail</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modals --}}
<div id="verifModal" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white p-6 rounded-2xl w-full max-w-sm">
        <h2 class="text-lg font-bold mb-4">Verifikasi Pengaduan</h2>
        <form id="verifForm" action="" method="POST">
            @csrf @method('PUT')
            <select name="status" class="w-full border rounded-lg p-2 mb-4" onchange="toggleAlasan(this.value)">
                <option value="Diterima">Diterima</option>
                <option value="Ditolak">Ditolak</option>
            </select>
            <div id="alasanDiv" class="hidden">
                <textarea name="alasan_penolakan" class="w-full border rounded-lg p-2 mb-4" rows="3" placeholder="Alasan penolakan..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('verifModal').classList.add('hidden')" class="px-4 py-2 text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white p-8 rounded-2xl w-full max-w-lg shadow-2xl">
        <h2 class="text-2xl font-bold mb-6 text-blue-900">Detail Lengkap</h2>
        <div id="modalContent" class="space-y-3 text-gray-700"></div>
        <button onclick="document.getElementById('detailModal').classList.add('hidden')" class="mt-6 w-full bg-blue-900 text-white py-3 rounded-xl font-bold">Tutup</button>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling khusus search box DataTables agar sesuai tema */
    .dt-search input { border: 1px solid #d1d5db !important; border-radius: 0.5rem !important; padding: 5px 10px !important; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tabelPengaduan').DataTable({
            "order": [] // Menggunakan urutan dari Controller
        });
    });

    function openVerificationModal(id) {
        const url = "{{ route('admin_dinas.update', ':id') }}".replace(':id', id);
        document.getElementById('verifForm').action = url;
        document.getElementById('verifModal').classList.remove('hidden');
    }

    function toggleAlasan(val) {
        document.getElementById('alasanDiv').classList.toggle('hidden', val !== 'Ditolak');
    }

    function showDetail(button) {
        const item = JSON.parse(button.getAttribute('data-pengaduan'));
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('modalContent').innerHTML = `
            <p><strong>Kode:</strong> ${item.kode_pengaduan}</p>
            <p><strong>Nama:</strong> ${item.nama_pelapor}</p>
            <p><strong>No WA:</strong> ${item.no_wa}</p>
            <p><strong>Lokasi:</strong> ${item.lokasi}</p>
            <p><strong>Isi:</strong> ${item.isi_pengaduan}</p>
            ${item.catatan_bidang ? `<p class="text-red-600"><strong>Catatan Bidang:</strong> ${item.catatan_bidang}</p>` : ''}
            ${item.alasan_penolakan ? `<p class="text-red-600"><strong>Alasan Ditolak:</strong> ${item.alasan_penolakan}</p>` : ''}
            ${item.foto_bukti ? `<img src="/storage/${item.foto_bukti}" class="w-full h-48 object-cover rounded-xl mt-4 border">` : '<p>Tidak ada foto.</p>'}
        `;
    }
</script>
@endpush
