@extends('layouts.admin_layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-blue-900 mb-6">Dashboard Admin Bidang</h1>
    <p class="text-gray-600 mb-8">Informasi pengaduan untuk bidang Anda.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border-b-4 border-blue-900">
            <h3 class="font-bold text-gray-700">Grafik Aduan Pertahun</h3>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-b-4 border-blue-900">
            <h3 class="font-bold text-gray-700">Grafik Aduan Pebulan</h3>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-b-4 border-blue-900">
            <h3 class="font-bold text-gray-700">Grafik Aduan Perhari</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-900 text-white p-6 rounded-lg shadow">
            <h3 class="font-bold">Total Aduan Diterima</h3>
        </div>
        <div class="bg-red-700 text-white p-6 rounded-lg shadow">
            <h3 class="font-bold">Total Aduan Ditolak</h3>
        </div>
    </div>
</div>
@endsection
