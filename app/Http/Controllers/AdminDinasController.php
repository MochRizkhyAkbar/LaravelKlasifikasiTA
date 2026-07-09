<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan; // Pastikan Model ini ada
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan package dompdf terinstall

class AdminDinasController extends Controller
{
    /**
     * Menampilkan halaman Kelola Pengaduan
     */
    public function index(Request $request)
    {
        // 1. Ambil data pengaduan
        $query = Pengaduan::query();

        // 2. Logika untuk Filtering berdasarkan Bidang (sesuai nama kolom di model: kategori_ai)
        if ($request->has('bidang') && $request->bidang != '') {
            $query->where('kategori_ai', $request->bidang);
        }

        $pengaduans = $query->get();

        // 3. Kirimkan data $pengaduans ke view
        return view('admin_dinas.kelola_pengaduan', compact('pengaduans'));
    }

    /**
     * Memproses pengubahan status pengaduan
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Diterima,Ditolak',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->status = $request->status;
        $pengaduan->save();

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui!');
    }

    /**
     * Menghasilkan file PDF untuk laporan pengaduan
     */
    public function exportPdf(Request $request)
    {
        $query = Pengaduan::query();

        if ($request->has('bidang') && $request->bidang != '') {
            $query->where('kategori_ai', $request->bidang);
        }

        $data = $query->get();

        // Pastikan kamu sudah membuat file resources/views/admin_dinas/cetak_pdf.blade.php
        // Ganti compact('data') dengan array eksplisit
        $pdf = Pdf::loadView('admin_dinas.cetak_pdf', ['data' => $data]);

        return $pdf->download('Laporan_Pengaduan_PUTR_' . date('d-m-Y') . '.pdf');
    }

    /**
     * Mengarah ke manajemen user
     */
    public function manajemenUser()
    {
        return view('admin_dinas.manajemen_user');
    }
}
