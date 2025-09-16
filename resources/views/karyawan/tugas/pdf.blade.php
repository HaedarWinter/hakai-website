<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Tugas Karyawan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2, h3 { margin: 0; padding: 4px 0; }
    </style>
</head>
<body>
<h2>Laporan Tugas Karyawan</h2>
<p>Tanggal cetak: {{ $tanggal }} {{ $jam }}</p>
@if($tugas)
<@foreach($tugas as $t)

        <h3>Detail Tugas</h3>
        <table>
            <tr>
                <th>Nama Karyawan</th>
                <td>{{ $t->user->name }}</td>
            </tr>
            <tr>
                <th>Tugas</th>
                <td>{{ $t->tugas }}</td>
            </tr>
            <tr>
                <th>Tanggal Mulai</th>
                <td>{{ $t->tanggal_mulai }}</td>
            </tr>
            <tr>
                <th>Tanggal Selesai</th>
                <td>{{ $t->tanggal_selesai }}</td>
            </tr>
        </table>
        @endforeach

@else
    <p><i>Belum ada tugas yang diberikan.</i></p>
@endif
</body>
</html>
