<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDinasController extends Controller
{
    /**
     * 1. Menampilkan halaman Kelola Pengaduan
     */
    public function index(Request $request)
    {
        $query = Pengaduan::query();

        if ($request->has('bidang') && $request->bidang != '') {
            $query->where('kategori_ai', $request->bidang);
        }

        // Menggunakan orderByRaw untuk menentukan prioritas urutan status
        $pengaduans = $query->orderByRaw("
            CASE
                WHEN status = 'Pending' THEN 1
                WHEN status = 'Dikembalikan' THEN 2
                WHEN status = 'Diterima' THEN 3
                WHEN status = 'Diproses' THEN 4
                WHEN status = 'Didisposisikan' THEN 5
                WHEN status = 'Selesai' THEN 6
                ELSE 7
            END ASC
        ")
        ->orderBy('created_at', 'DESC') // Data terbaru tetap muncul paling atas di dalam kelompok status yang sama
        ->get();

        return view('admin_dinas.kelola_pengaduan', compact('pengaduans'));
    }

    /**
     * 2. Memproses pengubahan status pengaduan & Alasan Penolakan
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Diterima,Ditolak',
            'alasan_penolakan' => 'required_if:status,Ditolak|nullable|string|max:500',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);

        $pengaduan->status = $request->status;

        if ($request->status === 'Ditolak') {
            $pengaduan->alasan_penolakan = $request->alasan_penolakan;
        } else {
            $pengaduan->alasan_penolakan = null;
        }

        $pengaduan->save();

        // Ditambahkan alert success
        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui!');
    }

    /**
     * 3. Menghasilkan file PDF untuk laporan pengaduan
     */
    public function exportPdf(Request $request)
    {
        $query = Pengaduan::query();

        if ($request->has('bidang') && $request->bidang != '') {
            $query->where('kategori_ai', $request->bidang);
        }

        $data = $query->get();

        // Cek jika data kosong sebelum export
        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor!');
        }

        $pdf = Pdf::loadView('admin_dinas.cetak_pdf', ['data' => $data]);

        // Ditambahkan alert success (via session sebelum download)
        // Catatan: Karena download PDF mengakhiri request, notifikasi biasanya
        // lebih efektif muncul SEBELUM user mengklik download atau via JS.
        return $pdf->download('Laporan_Pengaduan_PUTR_' . date('d-m-Y') . '.pdf');
    }

    /**
     * 4. Mengarah ke manajemen user
     */
    public function manajemenUser()
    {
        return view('admin_dinas.manajemen_user');
    }
}
