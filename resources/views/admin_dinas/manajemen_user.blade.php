@extends('layouts.admin_layout')

@section('content')
<div class="p-6" x-data="{ openModal: false, openEditModal: 0 }">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-blue-900">Manajemen User</h1>
            <p class="text-gray-500">Kelola akun petugas dinas dan akses sistem.</p>
        </div>
        <button @click="openModal = true" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg shadow-md transition">
            + Tambah User Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <table id="tabelUser" class="w-full text-left text-sm display nowrap">
            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                <tr>
                    <th class="px-6 py-4">NO</th>
                    <th class="px-6 py-4">WAKTU DAFTAR</th>
                    <th class="px-6 py-4">EMAIL</th>
                    <th class="px-6 py-4">ROLE</th>
                    <th class="px-6 py-4">STATUS</th>
                    <th class="px-6 py-4 text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $index => $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">{{ $user->created_at->format('d/M/Y') }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <button @click="openEditModal = {{ $user->id }}" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal Edit Profil Lengkap --}}
    @foreach($users as $user)
    <div x-show="openEditModal === {{ $user->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg w-1/3" @click.away="openEditModal = 0">
            <h2 class="text-xl font-bold mb-4">Edit Data User</h2>
            <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                @csrf @method('PUT')

                <label class="block mb-1 text-sm font-semibold">Nama:</label>
                <input type="text" name="name" value="{{ $user->name }}" class="w-full border p-2 mb-2 rounded" required>

                <label class="block mb-1 text-sm font-semibold">Email:</label>
                <input type="email" name="email" value="{{ $user->email }}" class="w-full border p-2 mb-2 rounded" required>

                <label class="block mb-1 text-sm font-semibold">Role:</label>
                <select name="role" class="w-full border p-2 mb-2 rounded" required>
                    <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin Dinas</option>
                    <option value="bidangSDA" {{ $user->hasRole('bidangSDA') ? 'selected' : '' }}>Bidang SDA</option>
                    <option value="bidangBINKON" {{ $user->hasRole('bidangBINKON') ? 'selected' : '' }}>Bidang BINKON</option>
                    <option value="bidangTATARUANG" {{ $user->hasRole('bidangTATARUANG') ? 'selected' : '' }}>Bidang Tata Ruang</option>
                    <option value="bidangJALAN" {{ $user->hasRole('bidangJALAN') ? 'selected' : '' }}>Bidang Jalan</option>
                </select>

                <label class="block mb-1 text-sm font-semibold">Status:</label>
                <select name="status" class="w-full border p-2 mb-2 rounded">
                    <option value="Aktif" {{ $user->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Non-Aktif" {{ $user->status == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>

                <label class="block mt-4 mb-1 text-sm font-semibold text-red-600">Password Baru (Opsional):</label>
                <input type="password" name="password" placeholder="Masukkan password baru" class="w-full border p-2 mb-4 rounded">

                <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                <button type="button" @click="openEditModal = 0" class="ml-2 text-gray-500">Batal</button>
            </form>
        </div>
    </div>
    @endforeach

    {{-- Modal Tambah User --}}
    <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg w-1/3" @click.away="openModal = false">
            <h2 class="text-xl font-bold mb-4">Tambah User Baru</h2>
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Nama" class="w-full border p-2 mb-2 rounded" required>
                <input type="email" name="email" placeholder="Email" class="w-full border p-2 mb-2 rounded" required>
                <input type="password" name="password" placeholder="Password" class="w-full border p-2 mb-2 rounded" required>

                <label class="block text-sm font-semibold mt-2">Pilih Role:</label>
                <select name="role" class="w-full border p-2 mb-2 rounded" required>
                    <option value="admin">Admin Dinas</option>
                    <option value="bidangSDA">Bidang SDA</option>
                    <option value="bidangBINKON">Bidang BINKON</option>
                    <option value="bidangTATARUANG">Bidang Tata Ruang</option>
                    <option value="bidangJALAN">Bidang Jalan</option>
                </select>

                <label class="block text-sm font-semibold mt-2">Status:</label>
                <select name="status" class="w-full border p-2 mb-2 rounded">
                    <option value="Aktif">Aktif</option>
                    <option value="Non-Aktif">Non-Aktif</option>
                </select>

                <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded mt-4">Simpan User</button>
                <button type="button" @click="openModal = false" class="ml-2 text-gray-500">Batal</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    window.addEventListener('load', function() {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#tabelUser')) {
            $('#tabelUser').DataTable({
                responsive: true,
                language: {
                    search: "Cari User:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                }
            });
        }
    });
</script>
@endpush
