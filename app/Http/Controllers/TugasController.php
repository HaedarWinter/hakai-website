<?php

namespace App\Http\Controllers;

use App\Exports\TugasExport;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    /**
     * List semua data tugas.
     * Ini bakal dipanggil pas buka menu Data Tugas di admin.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->jabatan == 'Admin') {
            // Admin -> lihat semua tugas
            $data = [
                'title'     => 'Data Tugas',
                'menuAdminTugas' => 'active',
                // Tambah whereHas untuk memastikan hanya tugas dengan user yang valid
                'tugas' => Tugas::with(['user' => function($query) {
                    $query->orderBy('nama', 'asc');
                }])
                    ->whereHas('user') // Hanya ambil tugas yang memiliki user
                    ->orderByRaw("
                CASE
                    WHEN status = 'approved' THEN 1
                    WHEN status = 'rejected' THEN 2
                    ELSE 3
                END
            ")
                    ->orderBy('tanggal_mulai', 'asc')
                    ->get(),
            ];
            return view('admin/tugas/index', $data);

        } else {
            // Karyawan -> lihat tugas miliknya sendiri
            $data = [
                'title'     => 'Data Tugas',
                'menuKaryawanTugas' => 'active',
                'tugas'     => Tugas::with('user')
                    ->where('user_id', $user->id)
                    ->orderByRaw("
                CASE
                    WHEN status = 'approved' THEN 1
                    WHEN status = 'rejected' THEN 2
                    ELSE 3
                END
            ")
                    ->orderBy('tanggal_mulai', 'asc')
                    ->get(),
            ];
            return view('karyawan/tugas/index', $data);
        }
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
            'user'           => User::where('jabatan', 'Karyawan')->get(),
        ];

        return view('admin/tugas/create', $data);
    }

    /**
     * Simpan tugas baru ke database.
     * Sekalian update user biar is_tugas jadi true (artinya udah ada tugas).
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|max:255',
            'tugas' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ], [
            'user_id.required' => 'Nama Tidak Boleh Kosong',
            'tugas.required' => 'Tugas Tidak Boleh Kosong',
            'tanggal_mulai.required' => 'Tanggal Mulai Tidak Boleh Kosong',
            'tanggal_selesai.required' => 'Tanggal Selesai Tidak Boleh Kosong',
        ]);

        $user = User::findOrFail($request->user_id);

        $tugas = new Tugas;
        $tugas->user_id = $request->user_id;
        $tugas->tugas = $request->tugas;
        $tugas->tanggal_mulai = $request->tanggal_mulai;
        $tugas->tanggal_selesai = $request->tanggal_selesai;
        $tugas->status = 'pending';
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('tugas_files', $filename, 'public');
            $tugas->tugas_file = $path;
        }

        $tugas->save();

        return redirect()->route('tugas.index')->with('success', 'Tugas Berhasil Ditambahkan');
    }

    /**
     * Belum dipake, bisa buat detail tugas kalau dibutuhin.
     */
    public function show($id)
    {
        // Cek apakah $id adalah 'create' atau kata kunci lainnya
        if (!is_numeric($id)) {
            return redirect()->route('tugas.index')->with('error', 'ID tugas tidak valid');
        }

        $tugas = Tugas::with('user')->findOrFail($id);

        $data = [
            'title' => 'Detail Tugas',
            'menuAdminTugas' => 'active',
            'tugas' => $tugas,
        ];

        return view('admin/tugas/show', $data);
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
    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);

        // Hapus file jika ada
        if ($tugas->tugas_file) {
            \Storage::disk('public')->delete($tugas->tugas_file);
        }
        if ($tugas->karyawan_file) {
            \Storage::disk('public')->delete($tugas->karyawan_file);
        }

        $tugas->delete();

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dihapus!');
    }

    public function approve($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->status = 'approved';
        $tugas->save();

        return redirect()->back()->with('success', 'Tugas berhasil disetujui!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:255',
        ]);

        $tugas = Tugas::findOrFail($id);
        $tugas->status = 'rejected';
        $tugas->catatan = $request->catatan;
        $tugas->save();

        return back()->with('error', 'Tugas ditolak dengan catatan: ' . $request->catatan);
    }


    public function upload(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
        ]);

        $tugas = Tugas::findOrFail($id);

        // Pastikan tugas milik user yang sedang login
        if ($tugas->user_id != auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke tugas ini!');
        }

        // Simpan file ke storage
        $filePath = $request->file('file')->store('tugas_karyawan', 'public');

        // Simpan ke kolom karyawan_file (bukan tugas_file)
        $tugas->karyawan_file = $filePath;

        // Jika status masih pending, ubah menjadi submitted
        if ($tugas->status == 'pending') {
            $tugas->status = 'submitted';
        }

        $tugas->save();

        return back()->with('success', 'File berhasil diupload!');
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
        // Ambil user yang login
        $user = Auth::user();

        if ($user->jabatan == 'Admin') {
            $tugas = Tugas::with('user')->orderBy('created_at', 'DESC')->get();
            $totalTugas = $tugas->count();
            $today = now();

            // Status berdasarkan tanggal
            $tugasSelesai = $tugas->filter(fn($item) => $item->tanggal_selesai && $today->gte($item->tanggal_selesai))->count();
            $tugasSedangBerjalan = $tugas->filter(fn($item) =>
                $item->tanggal_mulai && $today->gte($item->tanggal_mulai) &&
                (!$item->tanggal_selesai || $today->lt($item->tanggal_selesai))
            )->count();
            $tugasBelumMulai = $tugas->filter(fn($item) => $item->tanggal_mulai && $today->lt($item->tanggal_mulai))->count();
            $tugasOverdue = $tugas->filter(fn($item) => $item->tanggal_selesai && $today->gt($item->tanggal_selesai))->count();

            // Statistik per user
            $totalUserBertugas = $tugas->pluck('user.id')->unique()->count();

            $stats = [
                'total' => $totalTugas,
                'total_user_bertugas' => $totalUserBertugas,
                'tugas_selesai' => $tugasSelesai,
                'tugas_sedang_berjalan' => $tugasSedangBerjalan,
                'tugas_belum_mulai' => $tugasBelumMulai,
                'tugas_overdue' => $tugasOverdue,
                'persentase_selesai' => $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100, 1) : 0,
                'persentase_sedang_berjalan' => $totalTugas > 0 ? round(($tugasSedangBerjalan / $totalTugas) * 100, 1) : 0,
                'persentase_belum_mulai' => $totalTugas > 0 ? round(($tugasBelumMulai / $totalTugas) * 100, 1) : 0,
                'persentase_overdue' => $totalTugas > 0 ? round(($tugasOverdue / $totalTugas) * 100, 1) : 0,
                'updated_at' => now()->format('d F Y H:i:s')
            ];

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
        } else {
            $data = [
                'tanggal' => now()->format('d-m-Y'),
                'jam' => now()->format('H:i:s'),
                'tugas' => Tugas::with('user')->where('user_id', $user->id)->get(),
            ];

            $filename = now()->format('Ymd_His');
            $pdf = Pdf::loadView('karyawan/tugas/pdf', $data) // <- diganti biar sesuai role
            ->setPaper('A4', 'portrait');

            return $pdf->download('DataTugas_'.$filename.'.pdf');
        }
    }
}
