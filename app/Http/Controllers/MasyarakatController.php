<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;

class MasyarakatController extends Controller
{
    public function create()
    {
        return view('masyarakat.input_pengaduan');
    }

    // Fungsi search yang diperbarui untuk mencari data
    public function search(Request $request)
    {
        $pengaduan = null;

        // Cek apakah ada input 'kode' dari form pencarian
        if ($request->has('kode') && $request->kode != '') {
            $pengaduan = Pengaduan::where('kode_pengaduan', $request->kode)->first();

            if (!$pengaduan) {
                return back()->with('error', 'Kode pengaduan tidak ditemukan.');
            }
        }

        return view('masyarakat.cari_pengaduan', compact('pengaduan'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama_pelapor' => 'required|string|max:255',
            'no_wa'        => 'required|string|max:15',
            'isi_pengaduan' => 'required|string',
            'lokasi'       => 'required|string|max:255',
            'foto_bukti'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Membuat Kode Unik
        $kode = 'PGD-' . substr(time(), -6);

        // 3. Simpan data ke database
        $pengaduan = new Pengaduan();
        $pengaduan->nama_pelapor  = $request->nama_pelapor;
        $pengaduan->no_wa         = $request->no_wa;
        $pengaduan->isi_pengaduan = $request->isi_pengaduan;
        $pengaduan->lokasi        = $request->lokasi;
        $pengaduan->kode_pengaduan = $kode;
        $pengaduan->status        = 'Pending'; // Default status

        if ($request->hasFile('foto_bukti')) {
            $path = $request->file('foto_bukti')->store('bukti', 'public');
            $pengaduan->foto_bukti = $path;
        }

        $pengaduan->save();

        // 4. Redirect kembali ke halaman form dengan pesan sukses
        return redirect()->route('pengaduan.create')
                         ->with('success', 'Pengaduan berhasil dikirim! Silakan simpan kode ini untuk melacak laporan Anda: <strong>' . $kode . '</strong>');
    }
}
