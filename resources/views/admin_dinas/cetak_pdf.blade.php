<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Pengaduan PUTR</h2>
    <p>Tanggal Cetak: {{ date('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Waktu</th>
                <th>Nama</th>
                <th>Aduan</th>
                <th>Lokasi</th>
                <th>Kategori (Sistem)</th>
                <th>Confidence (%)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->kode_pengaduan }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>{{ $item->nama_pelapor }}</td>
                <td>{{ $item->isi_pengaduan }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ $item->kategori_ai }}</td>
                <td>{{ $item->confidence_score }}%</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
