<table>
    <!-- Header Utama -->
    <thead>
    <!-- Judul Utama (Row 1) -->
    <tr>
        <th colspan="7" align="center">LAPORAN DATA TUGAS SISTEM - Hakai</th>
    </tr>

    <!-- Informasi Tanggal dan Jam (Row 2) -->
    <tr>
        <th colspan="4" align="center">Tanggal Cetak: {{ $tanggal }}</th>
        <th colspan="3" align="center">Pukul: {{ $jam }}</th>
    </tr>

    <!-- Baris kosong untuk spacing (Row 3) -->
    <tr>
        <th colspan="7">&nbsp;</th>
    </tr>

    <!-- Header Dashboard (Row 4) -->
    <tr>
        <th colspan="7" align="center"> DASHBOARD RINGKASAN DATA TUGAS</th>
    </tr>

    <!-- Header Ringkasan Total Tugas (Row 5) -->
    <tr>
        <th colspan="7" align="center">RINGKASAN TOTAL TUGAS</th>
    </tr>

    <!-- Detail Total Tugas (Row 6) -->
    <tr>
        <th align="center">Total Tugas</th>
        <th align="center">{{ $stats['total'] }} tugas</th>
        <th align="center">Total User Bertugas</th>
        <th align="center">{{ $stats['total_user_bertugas'] }} orang</th>
        <th colspan="2" align="center">Rata-rata per User</th>
        <th align="center">{{ $stats['total_user_bertugas'] > 0 ? round($stats['total'] / $stats['total_user_bertugas'], 1) : 0 }} tugas</th>
    </tr>

    <!-- Header Status Progress (Row 7) -->
    <tr>
        <th colspan="7" align="center">STATUS PROGRESS TUGAS</th>
    </tr>

    <!-- Detail Status Progress (Row 8) -->
    <tr>
        <th align="center">Selesai</th>
        <th align="center">{{ $stats['tugas_selesai'] }} ({{ $stats['persentase_selesai'] }}%)</th>
        <th align="center">Sedang Berjalan</th>
        <th align="center">{{ $stats['tugas_sedang_berjalan'] }} ({{ $stats['persentase_sedang_berjalan'] }}%)</th>
        <th align="center">Belum Mulai</th>
        <th align="center">{{ $stats['tugas_belum_mulai'] }} ({{ $stats['persentase_belum_mulai'] }}%)</th>
        <th align="center">Overdue: {{ $stats['tugas_overdue'] }}</th>
    </tr>

    <!-- Performance Insight (Row 9) -->
    <tr>
        <th colspan="2" align="center">Performance Rate</th>
        <th align="center">
            @if($stats['persentase_selesai'] >= 80)
                Sangat Baik
            @elseif($stats['persentase_selesai'] >= 60)
                Baik
            @elseif($stats['persentase_selesai'] >= 40)
                Cukup
            @else
                Perlu Peningkatan
            @endif
        </th>
        <th colspan="2" align="center">Status Overdue</th>
        <th align="center">
            @if($stats['persentase_overdue'] <= 5)
                Sangat Baik ({{ $stats['persentase_overdue'] }}%)
            @elseif($stats['persentase_overdue'] <= 15)
                Baik ({{ $stats['persentase_overdue'] }}%)
            @else
                Perlu Perhatian ({{ $stats['persentase_overdue'] }}%)
            @endif
        </th>
    </tr>

    <!-- Header Tabel Data (Row 10) -->
    <tr>
        <th align="center">No</th>
        <th align="center">Nama User</th>
        <th align="center">Tugas</th>
        <th align="center">Tanggal Mulai</th>
        <th align="center">Tanggal Selesai</th>
        <th align="center">Status Progress</th>
        <th align="center">Keterangan</th>
    </tr>
    </thead>

    <!-- Body Tabel -->
    <tbody>
    @forelse($tugas as $item)
        @php
            $today = now();
            $tanggalMulai = $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai) : null;
            $tanggalSelesai = $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai) : null;

            $status = 'Tidak Terdefinisi';
            $keterangan = '-';

            if ($tanggalMulai && $tanggalSelesai) {
                if ($today->gte($tanggalSelesai)) {
                    $status = 'Selesai';
                    $keterangan = 'Completed on: ' . $tanggalSelesai->format('d/m/Y');
                } elseif ($today->gte($tanggalMulai) && $today->lt($tanggalSelesai)) {
                    $status = 'Sedang Berjalan';
                    $sisaHari = $today->diffInDays($tanggalSelesai, false);
                    $keterangan = $sisaHari > 0 ? "Sisa {$sisaHari} hari" : 'Berakhir hari ini';
                } elseif ($today->lt($tanggalMulai)) {
                    $status = 'Belum Mulai';
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
            <!-- Nomor urut -->
            <td align="center">{{ $loop->iteration }}</td>

            <!-- Nama User -->
            <td align="left">{{ $item->user->nama ?? 'N/A' }}</td>

            <!-- Tugas -->
            <td align="left">{{ $item->tugas ?? 'N/A' }}</td>

            <!-- Tanggal Mulai -->
            <td align="center">
                {{ $tanggalMulai ? $tanggalMulai->format('d/m/Y') : 'N/A' }}
            </td>

            <!-- Tanggal Selesai -->
            <td align="center">
                {{ $tanggalSelesai ? $tanggalSelesai->format('d/m/Y') : 'N/A' }}
            </td>

            <!-- Status Progress dengan conditional formatting -->
            <td align="center">
                @if($status === 'Selesai')
                    <span style="background-color: #C8E6C9; color: #2E7D32; font-weight: bold; padding: 2px 8px; border-radius: 4px;">
                        {{ $status }}
                    </span>
                @elseif($status === 'Sedang Berjalan')
                    <span style="background-color: #FFF3E0; color: #E65100; font-weight: bold; padding: 2px 8px; border-radius: 4px;">
                        {{ $status }}
                    </span>
                @elseif($status === 'Belum Mulai')
                    <span style="background-color: #E3F2FD; color: #0D47A1; font-weight: bold; padding: 2px 8px; border-radius: 4px;">
                        {{ $status }}
                    </span>
                @else
                    <span style="background-color: #F5F5F5; color: #666; font-weight: bold; padding: 2px 8px; border-radius: 4px;">
                        {{ $status }}
                    </span>
                @endif
            </td>

            <!-- Keterangan dengan conditional styling untuk overdue -->
            <td align="center">
                @if(strpos($keterangan, 'Overdue') !== false)
                    <span style="background-color: #FFCDD2; color: #C62828; font-weight: bold; padding: 2px 8px; border-radius: 4px;">
                        {{ $keterangan }}
                    </span>
                @else
                    {{ $keterangan }}
                @endif
            </td>
        </tr>
    @empty
        <!-- Jika tidak ada data -->
        <tr>
            <td colspan="7" align="center" style="font-style: italic; color: #666; padding: 20px;">
                Tidak ada data tugas yang tersedia
            </td>
        </tr>
    @endforelse
    </tbody>

    <!-- Footer Summary -->
    <tfoot>
    <!-- Summary Status (berdasarkan Progress) -->
    <tr style="background-color: #E8F5E8; font-weight: bold; border-top: 2px solid #4CAF50;">
        <td colspan="2" align="center" style="color: #2E7D32;">
            SELESAI: {{ $stats['tugas_selesai'] }} ({{ $stats['persentase_selesai'] }}%)
        </td>
        <td colspan="2" align="center" style="color: #E65100;">
            SEDANG BERJALAN: {{ $stats['tugas_sedang_berjalan'] }} ({{ $stats['persentase_sedang_berjalan'] }}%)
        </td>
        <td colspan="2" align="center" style="color: #0D47A1;">
            BELUM MULAI: {{ $stats['tugas_belum_mulai'] }} ({{ $stats['persentase_belum_mulai'] }}%)
        </td>
        <td align="center" style="color: #C62828;">
            OVERDUE: {{ $stats['tugas_overdue'] }}
        </td>
    </tr>

    <!-- Summary Keseluruhan -->
    <tr style="background-color: #FFF9C4; font-weight: bold;">
        <td colspan="5" align="right" style="color: #F57F17;">TOTAL KESELURUHAN:</td>
        <td align="center" style="color: #2E7D32;">
            {{ $stats['tugas_selesai'] }} Selesai
        </td>
        <td align="center" style="color: #1976D2;">
            {{ $stats['total'] }} Total Tugas
        </td>
    </tr>

    <!-- Grand Total -->
    <tr style="background-color: #F5F5F5; font-weight: bold; border: 2px solid #666;">
        <td colspan="6" align="right" style="font-size: 12px;"> GRAND TOTAL TUGAS:</td>
        <td align="center" style="font-size: 14px; color: #1976D2;">
            <strong>{{ $stats['total'] }} TUGAS</strong>
        </td>
    </tr>

    <!-- Timestamp -->
    <tr>
        <td colspan="7" align="center" style="font-size: 10px; color: #666; padding-top: 15px;">
            <em> Laporan ini dibuat secara otomatis oleh Hakai Website pada {{ $stats['updated_at'] }}</em>
        </td>
    </tr>

    <!-- Performance Insight -->
    <tr>
        <td colspan="7" align="center" style="font-size: 10px; color: #4CAF50; padding-top: 5px;">
            <strong>
                INSIGHT: Tingkat penyelesaian tugas mencapai {{ $stats['persentase_selesai'] }}%
                @if($stats['persentase_selesai'] >= 80)
                    - Sangat Baik!
                @elseif($stats['persentase_selesai'] >= 60)
                    - Baik
                @elseif($stats['persentase_selesai'] >= 40)
                    - Cukup
                @else
                    - Perlu Peningkatan
                @endif

                @if($stats['tugas_overdue'] > 0)
                    | {{ $stats['tugas_overdue'] }} tugas mengalami keterlambatan
                @endif
            </strong>
        </td>
    </tr>

    <!-- Confidential -->
    <tr>
        <td colspan="7" align="center" style="font-size: 9px; color: #999; padding-top: 10px; border-top: 1px solid #DDD;">
            <strong>CONFIDENTIAL</strong> - Data ini bersifat rahasia dan hanya untuk penggunaan internal
        </td>
    </tr>
    </tfoot>
</table>
