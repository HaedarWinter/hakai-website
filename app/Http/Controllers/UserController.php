<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = array(
            'title' => 'Data User',
            'menuAdminUser' => 'active',
            'user' => User::orderBy('jabatan', 'ASC')->get(),
        );
        return view('admin/user/index', $data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = array(
            'title' => 'Tambah User',
            'menuAdminUser' => 'active',

        );
        return view('admin/user/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'jabatan' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ], [
            'nama.required' => 'Nama Tidak Boleh Kosong',
            'email.required' => 'Email Tidak Boleh Kosong',
            'email.unique' => 'Email Sudah Terdaftar',
            'jabatan.required' => 'Jabatan Harus Diisi',
            'password.required' => 'Password Tidak Boleh Kosong',
            'password.confirmed' => 'Password Konfirmasi Tidak Sama',
            'password.min' => 'Password Minimal 8 Karakter',
        ]);

        $user = new User;
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->jabatan = $request->jabatan;
        $user->password = Hash::make($request->password);
        $user->is_tugas = false;
        $user->save();
        return redirect()->route('user.index')->with('success', 'Data Berhasil Ditambahkan');

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = array(
            'title' => 'Edit User',
            'menuAdminUser' => 'active',
            'user' => User::findOrFail($id),

        );
        return view('admin/user/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'jabatan' => 'required|string',
            'password' => 'nullable|string|confirmed|min:8'
        ], [
            'nama.required' => 'Nama Tidak Boleh Kosong',
            'email.required' => 'Email Tidak Boleh Kosong',
            'email.unique' => 'Email Sudah Terdaftar',
            'jabatan.required' => 'Jabatan Harus Diisi',
        ]);

        $user = User::findOrFail($id);
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->jabatan = $request->jabatan;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return redirect()->route('user.index')->with('success', 'Data Berhasil Diperbarui');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        //pake DB agar user disimpan dulu sebelum dihapus(takutnya ada kesalahan)
        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
            $user->delete();
        });
            return redirect()->route('user.index')->with('success', 'Data Berhasil Dihapus');

    }


    public function excel(){
        $filename = now()->format('d-m-Y-H:i:s');
        return Excel::download(new UserExport, 'DataUser_'.$filename.'.xlsx');
    }

    public function pdf()
    {
        $users = User::orderBy('jabatan', 'ASC')->get();

        // Hitung statistik (mirip di UserExport)
        $adminCount = $users->where('jabatan', 'Admin')->count();
        $userCount = $users->whereNotIn('jabatan', ['Admin'])->count();

        $nonAdminUsers = $users->whereNotIn('jabatan', ['Admin']);
        $tugasSelesai = $nonAdminUsers->where('is_tugas', true)->count();
        $tugasBelumSelesai = $nonAdminUsers->where('is_tugas', false)->count();

        $userDitugaskan = $nonAdminUsers->where('is_tugas', true)->count();
        $userBelumDitugaskan = $nonAdminUsers->where('is_tugas', false)->count();

        $stats = [
            'total' => $users->count(),
            'admin_count' => $adminCount,
            'user_count' => $userCount,
            'tugas_selesai' => $tugasSelesai,
            'tugas_belum_selesai' => $tugasBelumSelesai,
            'persentase_selesai' => $nonAdminUsers->count() > 0
                ? round(($tugasSelesai / $nonAdminUsers->count()) * 100, 1)
                : 0,
            'user_ditugaskan' => $userDitugaskan,
            'user_belum_ditugaskan' => $userBelumDitugaskan,
            'user_persentase_selesai' => $userCount > 0
                ? round(($userDitugaskan / $userCount) * 100, 1)
                : 0,
            'updated_at' => now()->format('d F Y H:i:s')
        ];

        // Data yang dikirim ke blade
        $data = [
            'user' => $users,
            'stats' => $stats,
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H:i:s'),
        ];

        $filename = now()->format('Ymd_His');
        $pdf = Pdf::loadView('admin/user/pdf', $data)
            ->setPaper('A4', 'landscape');

        return $pdf->download('DataUser_'.$filename.'.pdf');
    }
}


