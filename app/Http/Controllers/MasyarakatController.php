<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Http;

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

        try {
            $response = Http::post('http://127.0.0.1:8000/predict',
            [
                'teks' => $request->isi_pengaduan
            ]);
            if (!$response ->successful()){
                return back()->with('error', 'Gagal memproses klasifikasi ai saat ini. Silahkan coba lagi nanti');
            }



            $aiResult = $response->json();

            // dd($aiResult['kategori_label']);


            if($aiResult['kategori_label'] == 'Bidang Sumber Daya Air(SDA)'){
                $aiResult['kategori_label'] = 'bidangSDA';
                }elseif($aiResult['kategori_label'] == 'bidang Jalan'){
                $aiResult['kategori_label'] = 'bidangJALAN';
            }elseif($aiResult['kategori_label'] == 'Bidang Tata Ruang'){
                $aiResult['kategori_label'] = 'bidangTATARUANG';
            }elseif($aiResult['kategori_label'] == 'bidang Bina Kontruksi dan Teknik(Binkon)'){
                $aiResult['kategori_label'] = 'bidangBINKON';
            }elseif($aiResult['kategori_label'] == 'bukan pupr'){
                $aiResult['kategori_label'] = 'BUKAN PUPR';
            }


            $kode = 'PGD-' . substr(time(), -6);

            $pengaduan = new Pengaduan();
            $pengaduan->nama_pelapor  = $request->nama_pelapor;
            $pengaduan->no_wa         = $request->no_wa;
            $pengaduan->isi_pengaduan = $request->isi_pengaduan;
            $pengaduan->lokasi        = $request->lokasi;
            $pengaduan->kode_pengaduan = $kode;
            $pengaduan->status        = 'Pending';
            $pengaduan->kategori_ai      = $aiResult['kategori_label'];

            if ($request->hasFile('foto_bukti')) {
                $path = $request->file('foto_bukti')->store('bukti', 'public');
                $pengaduan->foto_bukti = $path;
            }

            $pengaduan->save();

            return redirect()->route('pengaduan.create')
            ->with('success', 'Pengaduan berhasil dikirim! Kode: <strong>' . $kode . '</strong>');


            } catch (\exception $e) {
            // dd($e);
            return back()->with('error', 'Koneksi ke server AI terputus: ' . $e->getMessage());
        }


    }
}
