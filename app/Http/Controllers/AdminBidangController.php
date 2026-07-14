<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBidangController extends Controller
{
    // Menampilkan daftar pengaduan yang masuk ke bidang terkait
    public function index()
    {
        $user = User::find(Auth::id());
        $roleBidang = $user->getRoleNames()->first();

        // Mengambil data termasuk status 'Selesai'
        $pengaduans = Pengaduan::with('user')
            ->where('kategori_ai', $roleBidang)
            ->whereIn('status', ['Pending', 'Diterima', 'Diproses', 'Didisposisikan', 'Dikembalikan', 'Selesai'])
            ->orderByRaw("
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

        return view('admin_bidang.tindaklanjuti', compact('pengaduans'));
    }

    public function dashboard()
    {
        $user = User::find(Auth::id());
        $roleBidang = $user->getRoleNames()->first();
        $query = Pengaduan::where('kategori_ai', $roleBidang);

        // Statistik untuk Kartu
        $statistik = [
            'diterima' => (clone $query)->where('status', 'Diterima')->count(),
            'diproses' => (clone $query)->where('status', 'Diproses')->count(),
            'selesai'  => (clone $query)->where('status', 'Selesai')->count(),
            'dikembalikan'  => (clone $query)->where('status', 'Dikembalikan')->count(),
        ];

        // 1. Data Grafik Per Tahun
        $grafikTahun = (clone $query)
            ->selectRaw("strftime('%Y', created_at) as tahun, COUNT(*) as total")
            ->groupBy('tahun')
            ->pluck('total', 'tahun')->toArray();

        // 2. Data Grafik Per Bulan
        $dataBulananRaw = (clone $query)
            ->selectRaw("strftime('%m', created_at) as bulan, COUNT(*) as total")
            ->groupBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        $grafikBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $grafikBulan[] = $dataBulananRaw[str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0;
        }

        // 3. Data Grafik 7 Hari Terakhir
        $grafikHari = (clone $query)
            ->selectRaw("date(created_at) as tanggal, COUNT(*) as total")
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal')->toArray();

        return view('admin_bidang.dashboard', compact('statistik', 'grafikTahun', 'grafikBulan', 'grafikHari'));
    }

    // Memproses update status/kategori oleh Admin Bidang
    public function update(Request $request, $id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        if ($request->filled('kategori_baru')) {
            $pengaduan->kategori_ai = $request->kategori_baru;
            $pengaduan->status = 'Didisposisikan';
        } else {
            $pengaduan->status = $request->status;
        }

        $pengaduan->catatan_bidang = $request->catatan;
        $pengaduan->save();

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui!');
    }
}
