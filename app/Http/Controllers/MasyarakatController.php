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

    public function search(Request $request)
    {
        $pengaduan = null;

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
        $request->validate([
            'nama_pelapor' => 'required|string|max:255',
            'no_wa'        => 'required|string|max:15',
            'isi_pengaduan' => 'required|string',
            'lokasi'       => 'required|string|max:255',
            'foto_bukti'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $kode = 'PGD-' . substr(time(), -6);

        $pengaduan = new Pengaduan();
        $pengaduan->nama_pelapor  = $request->nama_pelapor;
        $pengaduan->no_wa         = $request->no_wa;
        $pengaduan->isi_pengaduan = $request->isi_pengaduan;
        $pengaduan->lokasi        = $request->lokasi;
        $pengaduan->kode_pengaduan = $kode;
        $pengaduan->status        = 'Pending';

        if ($request->hasFile('foto_bukti')) {
            $path = $request->file('foto_bukti')->store('bukti', 'public');
            $pengaduan->foto_bukti = $path;
        }

        $pengaduan->save();

        return redirect()->route('pengaduan.create')
                         ->with('success', 'Pengaduan berhasil dikirim! Kode: <strong>' . $kode . '</strong>');
    }
}
