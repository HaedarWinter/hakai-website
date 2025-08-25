@extends('layouts.app')
@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user-friends"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between align-items-center py-3">
            <div class="mb-1 mr-2">
            <a href="#" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i>
                Tambah Data
            </a>
            </div>

            <div>
                <span>Export ke</span>
                <a href="#" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i>
                    Excel
                </a>
                <span class="mx-1">atau</span>
                <a href="#" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i>
                    PDF
                </a>
            </div>

        </div>
        <div class="card-body">
            <div class="table-responsive ">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark text-center">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 100px;">
                            <i class="fas fa-cog"></i>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Diwan Perwakilan Ruhyat</td>
                        <td>koruptorindonesia1945@gmail.com</td>
                        <td class="text-center">
                            <span class="badge badge-success badge-pill p-2">Admin</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger badge-pill p-2">Belum Ditugaskan</span>
                        </td>
                        <td class="text-center">
                            <a href="#" class="btn btn-sm btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="#" class="btn btn-sm btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
