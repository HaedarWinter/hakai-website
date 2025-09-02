<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Ambil Total Uaer
        $totalUser = User::count();
        $totalAdmin = User::where('jabatan', 'admin')->count();
        $totalKaryawan = User::where('jabatan', 'karyawan')->count();

        //Hitungan Persentasi Total User
        $persentasiAdmin = $totalUser > 0 ? round(($totalAdmin/$totalUser) * 100, 1) : 0;
        $persentasiKaryawan = $totalUser > 0 ? round(($totalKaryawan/$totalUser) * 100, 1) : 0;

        //Format ke Ribuan
        $formattedTotalUser = number_format($totalUser, 0, ',', '.') . ' User';
        $formattedTotalAdmin = number_format($totalAdmin, 0, ',', '.') . ' Admin' ;
        $formattedTotalKaryawan = number_format($totalKaryawan, 0, ',', '.') . ' Karyawan' ;

        $data = array(
            'title' => 'Dashboard',
            'menuDashboard' => 'active',
            'totalUser' => $formattedTotalUser,
            'totalAdmin' => $formattedTotalAdmin,
            'totalKaryawan' => $formattedTotalKaryawan,
            'persentasiAdmin' => $persentasiAdmin,
            'persentasiKaryawan' => $persentasiKaryawan,

        );
        return view('dashboard', $data);


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
