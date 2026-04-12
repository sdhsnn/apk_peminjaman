<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #062c21; color: white; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 30px; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI PEMINJAMAN ALAT</h2>
        <p>SPORTRENT MANAGEMENT SYSTEM</p>
        @if(isset($tgl_mulai) && isset($tgl_selesai) && $tgl_mulai && $tgl_selesai)
            <p>Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
        @endif
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporans as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data->created_at->format('d/m/Y') }}</td>
                <td>{{ $data->user->name ?? '-' }}</td>
                <td>{{ $data->alat->nama_alat ?? '-' }}</td>
                <td class="text-center">{{ $data->jumlah }}</td>
                <td class="text-center">{{ strtoupper($data->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>