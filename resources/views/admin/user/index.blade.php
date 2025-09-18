@extends('layouts.app')
@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user-friends"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between align-items-center py-3">
            <div class="mb-1 mr-2">
            <a href="{{route('user.create')}}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i>
                Tambah Data
            </a>
            </div>

            <div>
                <span>Export ke</span>
                <a href="{{route('user.excel')}}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i>
                    Excel
                </a>
                <span class="mx-1">atau</span>
                <a href="{{route('user.pdf')}}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i>
                    PDF
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive ">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark text-center">
                    <tr>
                        <th>No</th>
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
                    @foreach ($user as $item)
                        <tr>
                            <td class="text-center">{{ $loop -> iteration }}</td>
                            <td>{{$item -> nama}}</td>
                            <td class="text-center">
                                <span class="badge badge-dark">
                                    {{ $item -> email }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item -> jabatan == 'Admin')
                                    <span class="badge badge-success "> {{ $item -> jabatan }}</span>
                                    @else
                                    <span class="badge badge-warning"> {{ $item -> jabatan }}</span>
                                    @endif
                            <td class="text-center">
                                @if($item->jabatan == 'Admin')
                                    @if($item->has_assigned_users ?? false)
                                        <span class="badge badge-success">Admin Sudah Menugaskan</span>
                                    @else
                                        <span class="badge badge-info">Admin Belum Menugaskan</span>
                                    @endif
                                @else
                                    @if($item->is_tugas)
                                        <span class="badge badge-success">Sudah Ditugaskan</span>
                                    @else
                                        <span class="badge badge-danger">Belum Ditugaskan</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{route('user.edit', $item ->id )}}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#exampleModal{{$item -> id}}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @include('admin/user/modal')

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            // Tampilkan notifikasi error jika ada
            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal Hapus',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
            @endif

            // Tampilkan notifikasi success jika ada
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
            @endif
        });
    </script>
@endsection
