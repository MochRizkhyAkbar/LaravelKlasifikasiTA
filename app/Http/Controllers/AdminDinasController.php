<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class AdminDinasController extends Controller
{
    /**
     * 1. Menampilkan Halaman Dashboard
     */
    public function dashboard()
    {
        // 1. Statistik
        // Gunakan query yang menangkap semua data tanpa terkecuali
        $statistik = [
        // 1. Filter spesifik untuk Menunggu Verifikasi
        'pending' => Pengaduan::where('status', 'Pending')->count(),

        // 2. Filter spesifik untuk Ditolak
        'ditolak' => Pengaduan::where('status', 'Ditolak')->count(),

        // 3. Filter untuk Terverifikasi (Diteruskan, Sedang Diproses, Selesai)
        // Kita hindari menggunakan whereNotIn agar tidak tercampur
        'diterima' => Pengaduan::where(function($query) {
            $query->where('status', 'Diproses')
                  ->orWhere('status', 'Diterima')
                  ->orWhere('status', 'Selesai');
        })->count(),
    ];

        // 2. Data Grafik Per Bulan
        // Kita ambil data sebagai array dan kita isi bulan yang kosong dengan 0
        $dataBulan = Pengaduan::select(DB::raw("strftime('%m', created_at) as bulan"), DB::raw('count(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan', 'ASC')
            ->pluck('total', 'bulan');

        // Pastikan grafikBulan selalu berisi 12 bulan (Jan-Des) agar tidak loncat
        $grafikBulan = collect(range(1, 12))->mapWithKeys(function ($m) use ($dataBulan) {
            $key = str_pad($m, 2, '0', STR_PAD_LEFT);
            return [$key => $dataBulan->get($key, 0)];
        });

        // 3. Data Grafik Hari (7 hari terakhir)
        $grafikHari = Pengaduan::select(DB::raw("date(created_at) as tanggal"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->pluck('total', 'tanggal');

        $grafikTahun = Pengaduan::select(DB::raw("strftime('%Y', created_at) as tahun"), DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun', 'ASC')
            ->pluck('total', 'tahun');

        return view('admin_dinas.dashboard', compact('statistik', 'grafikTahun', 'grafikBulan', 'grafikHari'));
    }

    /**
     * 2. Menampilkan halaman Kelola Pengaduan
     */
    public function index(Request $request)
    {
        $query = Pengaduan::query();

        if ($request->has('bidang') && $request->bidang != '') {
            $query->where('kategori_ai', $request->bidang);
        }

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
        ->orderBy('created_at', 'DESC')
        ->get();

        return view('admin_dinas.kelola_pengaduan', compact('pengaduans'));
    }

    /**
     * 3. Memproses pengubahan status pengaduan
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
        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui!');
    }

    /**
     * 4. Menghasilkan file PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Pengaduan::query();
        if ($request->has('bidang') && $request->bidang != '') {
            $query->where('kategori_ai', $request->bidang);
        }
        $data = $query->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor!');
        }

        $pdf = Pdf::loadView('admin_dinas.cetak_pdf', ['data' => $data]);
        return $pdf->download('Laporan_Pengaduan_PUTR_' . date('d-m-Y') . '.pdf');
    }

    /**
     * 5. Mengarah ke manajemen user
     */
    public function manajemenUser()
    {
        return view('admin_dinas.manajemen_user');
    }
}
