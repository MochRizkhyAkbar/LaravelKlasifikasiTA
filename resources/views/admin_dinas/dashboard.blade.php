@extends('layouts.admin_layout')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-blue-900 mb-6">Dashboard Admin Dinas</h1>
    <p class="text-gray-600 mb-8">Selamat datang, {{ auth()->user()->name }}. Berikut adalah ringkasan informasi pengaduan.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-900 text-white p-6 rounded-lg shadow">
            <h3 class="font-bold">Total Aduan Terverifikasi</h3>
            <p class="text-3xl font-extrabold mt-2">{{ $totalTerverifikasi }}</p>
        </div>
        <div class="bg-yellow-600 text-white p-6 rounded-lg shadow">
            <h3 class="font-bold">Total Aduan Belum Terverifikasi</h3>
            <p class="text-3xl font-extrabold mt-2">{{ $totalPending }}</p>
        </div>
        <div class="bg-red-700 text-white p-6 rounded-lg shadow">
            <h3 class="font-bold">Total Aduan Ditolak</h3>
            <p class="text-3xl font-extrabold mt-2">{{ $totalDitolak }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border-b-4 border-blue-900">
            <h3 class="font-bold text-gray-700 mb-4">Aduan Per Tahun</h3>
            <canvas id="grafikTahun"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-b-4 border-yellow-600">
            <h3 class="font-bold text-gray-700 mb-4">Aduan Per Bulan</h3>
            <canvas id="grafikBulan"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-b-4 border-red-700">
            <h3 class="font-bold text-gray-700 mb-4">Aduan 7 Hari Terakhir</h3>
            <canvas id="grafikHari"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function buatChart(id, labels, data, labelText, color) {
        new Chart(document.getElementById(id).getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: labelText,
                    data: data,
                    backgroundColor: color,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    // Ambil data dari PHP ke variabel JS dengan aman
    const dataTahunKeys = {!! json_encode($grafikPerTahun->keys()) !!};
    const dataTahunValues = {!! json_encode($grafikPerTahun->values()) !!};

    // Tambahkan ->toArray() sebelum diolah oleh fungsi PHP
    const dataBulanValues = {!! json_encode(array_values(array_replace(array_fill(0, 12, 0), $grafikPerBulan->mapWithKeys(fn($item, $key) => [(int)$key - 1 => $item])->toArray()))) !!};
    const dataHariKeys = {!! json_encode($grafikPerHari->keys()) !!};
    const dataHariValues = {!! json_encode($grafikPerHari->values()) !!};

    // Inisialisasi 3 Grafik
    buatChart('grafikTahun', dataTahunKeys, dataTahunValues, 'Jumlah', '#1e3a8a');
    buatChart('grafikBulan', ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'], dataBulanValues, 'Jumlah', '#d97706');
    buatChart('grafikHari', dataHariKeys, dataHariValues, 'Jumlah', '#b91c1c');
</script>
@endpush
