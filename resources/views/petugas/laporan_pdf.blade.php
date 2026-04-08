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
        .status { font-weight: bold; text-transform: uppercase; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI PEMINJAMAN ALAT</h2>
        <p>SPORTRENT MANAGEMENT SYSTEM</p>
        @if(isset($tgl_mulai) && isset($tgl_selesai))
            <p style="font-size: 10px;">Periode: {{ $tgl_mulai }} s/d {{ $tgl_selesai }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporans as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data->created_at->format('d/m/Y') }}</td>
                <td>{{ $data->user->name }}</td>
                <td>{{ $data->alat->nama_alat }} ({{ $data->jumlah }})</td>
                <td><span class="status">{{ $data->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>