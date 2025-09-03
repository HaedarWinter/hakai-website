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

            <h1>Ini Halaman Karyawan</h1>

        </div>
    </div>
@endsection
