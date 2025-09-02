<?php

namespace App\Http\Controllers;

use App\Exports\TugasExport;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TugasController extends Controller
{
    /**
     * List semua data tugas.
     * Ini bakal dipanggil pas buka menu Data Tugas di admin.
     */
    public function index()
    {
        $data = [
            'title'     => 'Data Tugas',
            'menuTugas' => 'active',
            // sekalian join sama user biar bisa nampilin nama karyawan
            'tugas'     => Tugas::with('user')->get(),
        ];

        return view('admin/tugas/index', $data);
    }

    /**
     * Form buat nambahin tugas baru.
     * Ambil user yang jabatan = karyawan dan belum ada tugas (is_tugas = false).
     */
    public function create()
    {
        $data = [
            'title'          => 'Tambah Tugas',
            'menuAdminTugas' => 'active',
            'user'           => User::where('jabatan', 'karyawan')
                ->where('is_tugas', false)
                ->get(),
        ];

        return view('admin/tugas/create', $data);
    }

    /**
     * Simpan tugas baru ke database.
     * Sekalian update user biar is_tugas jadi true (artinya udah ada tugas).
     */
    public function store(Request $request)
    {
        // validasi form biar ga ada data kosong
        $request->validate([
            'user_id'           => 'required|string|max:255',
            'tugas'             => 'required',
            'tanggal_mulai'     => 'required',
            'tanggal_selesai'   => 'required',
        ], [
            'user_id.required'          => 'Nama Tidak Boleh Kosong',
            'tugas.required'            => 'Tugas Tidak Boleh Kosong',
            'tanggal_mulai.required'    => 'Tanggal Mulai Tidak Boleh Kosong',
            'tanggal_selesai.required'  => 'Tanggal Selesai Tidak Boleh Kosong',
        ]);

        // ambil user sesuai id yang dipilih
        $user = User::findOrFail($request->user_id);

        // bikin record tugas baru
        $tugas = new Tugas;
        $tugas->user_id         = $request->user_id;
        $tugas->tugas           = $request->tugas;
        $tugas->tanggal_mulai   = $request->tanggal_mulai;
        $tugas->tanggal_selesai = $request->tanggal_selesai;
        $tugas->save();

        // update user jadi "udah punya tugas"
        $user->is_tugas = true;
        $user->save();

        return redirect()->route('tugas.index')->with('success', 'Tugas Berhasil Ditambahkan');
    }

    /**
     * Belum dipake, bisa buat detail tugas kalau dibutuhin.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Form edit tugas.
     * Data tugas diambil sekalian sama relasi user biar gampang.
     */
    public function edit(string $id)
    {
        $data = [
            'title'          => 'Edit Tugas',
            'menuAdminTugas' => 'active',
            'tugas'          => Tugas::with('user')->findOrFail($id),
        ];

        return view('admin/tugas/update', $data);
    }

    /**
     * Update data tugas yang udah ada.
     * Disini cuma ubah data tugas aja, user_id ga diubah.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tugas'             => 'required',
            'tanggal_mulai'     => 'required',
            'tanggal_selesai'   => 'required',
        ], [
            'tugas.required'            => 'Tugas Tidak Boleh Kosong',
            'tanggal_mulai.required'    => 'Tanggal Mulai Tidak Boleh Kosong',
            'tanggal_selesai.required'  => 'Tanggal Selesai Tidak Boleh Kosong',
        ]);

        $tugas = Tugas::findOrFail($id);
        $tugas->tugas           = $request->tugas;
        $tugas->tanggal_mulai   = $request->tanggal_mulai;
        $tugas->tanggal_selesai = $request->tanggal_selesai;
        $tugas->save();

        return redirect()->route('tugas.index')->with('success', 'Data Tugas Berhasil Diperbarui');
    }

    /**
     * Hapus tugas.
     * Setelah tugas dihapus, status user dikembalikan lagi ke is_tugas = false.
     */
    public function destroy(string $id)
    {
        // pakai db agar data aman semisal data hapus nya error
        DB::transaction(function () use ($id) {
            $tugas = Tugas::findOrFail($id);

            // simpan user_id dulu sebelum delete
            $userId = $tugas->user_id;

            $tugas->delete();

            // update status user biar bisa dapet tugas baru lagi
            $user = User::find($userId);
            if ($user) {
                $user->is_tugas = false;
                $user->save();
            }
        });

        return redirect()->route('tugas.index')->with('success', 'Data Tugas Berhasil Dihapus');
    }

    public function excel()
    {
        $filename = now()->format('Ymd_His');
        return Excel::download(new TugasExport, 'DataTugas_'.$filename.'.xlsx');
    }

    /**
     * Export data tugas to PDF
     */
    public function pdf()
    {
        $tugas = Tugas::with('user')->orderBy('created_at', 'DESC')->get();

        // Hitung statistik (mirip di TugasExport)
        $totalTugas = $tugas->count();
        $today = now();

        // Status berdasarkan tanggal
        $tugasSelesai = $tugas->filter(function($item) use ($today) {
            return $item->tanggal_selesai && $today->gte($item->tanggal_selesai);
        })->count();

        $tugasSedangBerjalan = $tugas->filter(function($item) use ($today) {
            return $item->tanggal_mulai && $today->gte($item->tanggal_mulai) &&
                (!$item->tanggal_selesai || $today->lt($item->tanggal_selesai));
        })->count();

        $tugasBelumMulai = $tugas->filter(function($item) use ($today) {
            return $item->tanggal_mulai && $today->lt($item->tanggal_mulai);
        })->count();

        // Status overdue
        $tugasOverdue = $tugas->filter(function($item) use ($today) {
            return $item->tanggal_selesai && $today->gt($item->tanggal_selesai);
        })->count();

        // Statistik per user
        $totalUserBertugas = $tugas->pluck('user.id')->unique()->count();

        $stats = [
            'total' => $totalTugas,
            'total_user_bertugas' => $totalUserBertugas,
            'tugas_selesai' => $tugasSelesai,
            'tugas_sedang_berjalan' => $tugasSedangBerjalan,
            'tugas_belum_mulai' => $tugasBelumMulai,
            'tugas_overdue' => $tugasOverdue,
            'persentase_selesai' => $totalTugas > 0
                ? round(($tugasSelesai / $totalTugas) * 100, 1)
                : 0,
            'persentase_sedang_berjalan' => $totalTugas > 0
                ? round(($tugasSedangBerjalan / $totalTugas) * 100, 1)
                : 0,
            'persentase_belum_mulai' => $totalTugas > 0
                ? round(($tugasBelumMulai / $totalTugas) * 100, 1)
                : 0,
            'persentase_overdue' => $totalTugas > 0
                ? round(($tugasOverdue / $totalTugas) * 100, 1)
                : 0,
            'updated_at' => now()->format('d F Y H:i:s')
        ];

        // Data yang dikirim ke blade
        $data = [
            'tugas' => $tugas,
            'stats' => $stats,
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H:i:s'),
        ];

        $filename = now()->format('Ymd_His');
        $pdf = Pdf::loadView('admin/tugas/pdf', $data)
            ->setPaper('A4', 'landscape');

        return $pdf->download('DataTugas_'.$filename.'.pdf');
    }
}
