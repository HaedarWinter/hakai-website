<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Tugas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px;
            text-align: center;
        }
        thead th {
            background: #f5f5f5;
            font-weight: bold;
        }
        tfoot td {
            font-weight: bold;
            background: #eee;
        }
        .label-selesai {
            background-color: #C8E6C9;
            color: #2E7D32;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .label-berjalan {
            background-color: #FFF3E0;
            color: #E65100;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .label-belum {
            background-color: #E3F2FD;
            color: #0D47A1;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .label-overdue {
            background-color: #FFCDD2;
            color: #C62828;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .confidential {
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<h3 align="center">LAPORAN DATA TUGAS SISTEM - Hakai</h3>
<p align="center">
    Tanggal Cetak: {{ $tanggal }} | Pukul: {{ $jam }}
</p>

{{-- Ringkasan Data --}}
<table>
    <thead>
    <tr>
        <th>Total Tugas</th>
        <th>{{ $stats['total'] }} tugas</th>
        <th>Total User Bertugas</th>
        <th>{{ $stats['total_user_bertugas'] }} orang</th>
        <th>Rata-rata per User</th>
        <th>{{ $stats['total_user_bertugas'] > 0 ? round($stats['total'] / $stats['total_user_bertugas'], 1) : 0 }} tugas</th>
    </tr>
    </thead>
</table>

{{-- Status Progress --}}
<table>
    <thead>
    <tr>
        <th colspan="6">STATUS PROGRESS TUGAS</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Selesai</td>
        <td>{{ $stats['tugas_selesai'] }} ({{ $stats['persentase_selesai'] }}%)</td>
        <td>Sedang Berjalan</td>
        <td>{{ $stats['tugas_sedang_berjalan'] }} ({{ $stats['persentase_sedang_berjalan'] }}%)</td>
        <td>Belum Mulai</td>
        <td>{{ $stats['tugas_belum_mulai'] }} ({{ $stats['persentase_belum_mulai'] }}%)</td>
    </tr>
    <tr>
        <td colspan="2">Performance Rate</td>
        <td>
            @if($stats['persentase_selesai'] >= 80)
                Sangat Baik
            @elseif($stats['persentase_selesai'] >= 60)
                Baik
            @elseif($stats['persentase_selesai'] >= 40)
                Cukup
            @else
                Perlu Peningkatan
            @endif
        </td>
        <td colspan="2">Overdue Tasks</td>
        <td>{{ $stats['tugas_overdue'] }} ({{ $stats['persentase_overdue'] }}%)</td>
    </tr>
    </tbody>
</table>

{{-- Data Tugas --}}
<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Nama User</th>
        <th>Tugas</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status Progress</th>
        <th>Keterangan</th>
    </tr>
    </thead>
    <tbody>
    @forelse($tugas as $item)
        @php
            $today = now();
            $tanggalMulai = $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai) : null;
            $tanggalSelesai = $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai) : null;

            $status = 'Tidak Terdefinisi';
            $keterangan = '-';
            $statusClass = '';

            if ($tanggalMulai && $tanggalSelesai) {
                if ($today->gte($tanggalSelesai)) {
                    $status = 'Selesai';
                    $statusClass = 'label-selesai';
                    $keterangan = 'Completed: ' . $tanggalSelesai->format('d/m/Y');
                } elseif ($today->gte($tanggalMulai) && $today->lt($tanggalSelesai)) {
                    $status = 'Sedang Berjalan';
                    $statusClass = 'label-berjalan';
                    $sisaHari = $today->diffInDays($tanggalSelesai, false);
                    $keterangan = $sisaHari > 0 ? "Sisa {$sisaHari} hari" : 'Berakhir hari ini';
                } elseif ($today->lt($tanggalMulai)) {
                    $status = 'Belum Mulai';
                    $statusClass = 'label-belum';
                    $mulaiDalam = $today->diffInDays($tanggalMulai);
                    $keterangan = "Mulai dalam {$mulaiDalam} hari";
                }

                // Check overdue
                if ($today->gt($tanggalSelesai)) {
                    $terlambat = $tanggalSelesai->diffInDays($today);
                    $keterangan = "Overdue {$terlambat} hari";
                }
            }
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td align="left">{{ $item->user->nama ?? 'N/A' }}</td>
            <td align="left">{{ $item->tugas ?? 'N/A' }}</td>
            <td>{{ $tanggalMulai ? $tanggalMulai->format('d/m/Y') : 'N/A' }}</td>
            <td>{{ $tanggalSelesai ? $tanggalSelesai->format('d/m/Y') : 'N/A' }}</td>
            <td>
                <span class="{{ $statusClass }}">{{ $status }}</span>
            </td>
            <td>
                @if(strpos($keterangan, 'Overdue') !== false)
                    <span class="label-overdue">{{ $keterangan }}</span>
                @else
                    {{ $keterangan }}
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" align="center">Tidak ada data tugas</td>
        </tr>
    @endforelse
    </tbody>
    <tfoot>
    <tr>
        <td colspan="7" class="confidential">
            <strong>CONFIDENTIAL</strong> - Data ini hanya untuk internal.
            Report generated: {{ $stats['updated_at'] }}
        </td>
    </tr>
    </tfoot>
</table>

</body>
</html>
