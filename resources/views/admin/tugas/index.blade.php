@extends('layouts.app')
@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user-friends"></i>
        {{ $title }}
    </h1>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between align-items-center py-3">
            <div class="mb-1 mr-2">
                <a href="{{ route('tugas.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Data
                </a>
            </div>

            <div>
                <span>Export ke</span>
                <a href="{{route('tugas.excel')}}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i>
                    Excel
                </a>
                <span class="mx-1">atau</span>
                <a href="{{ route('pdf') }}" class="btn btn-sm btn-danger">
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
                        <th>Tugas</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Upload File</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 180px;">
                            <i class="fas fa-cog"></i>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($tugas as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->user->nama }}</td>
                            <td>{{ $item->tugas }}</td>
                            <td class="text-center">
                                <span class="badge badge-dark">{{ $item->tanggal_mulai }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-danger">{{ $item->tanggal_selesai }}</span>
                            </td>

                            {{-- Upload File --}}
                            <td class="text-center">
                                @if($item->tugas_file)
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalPreview{{ $item->id }}">
                                        <i class="fas fa-eye"></i> Lihat
                                    </button>

                                    {{-- Modal Preview --}}
                                    <div class="modal fade" id="modalPreview{{ $item->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-xl" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Preview Tugas - {{ $item->user->nama }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    @php $ext = pathinfo($item->tugas_file, PATHINFO_EXTENSION); @endphp

                                                    @if(in_array($ext, ['jpg','jpeg','png']))
                                                        <img src="{{ asset('storage/'.$item->tugas_file) }}" class="img-fluid rounded shadow">
                                                    @elseif($ext === 'pdf')
                                                        <iframe src="{{ asset('storage/'.$item->tugas_file) }}" width="100%" height="600px"></iframe>
                                                    @else
                                                        <a href="{{ asset('storage/'.$item->tugas_file) }}" class="btn btn-secondary" target="_blank">
                                                            Download File
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('tugas.approve', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('tugas.reject', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-secondary">Belum Upload</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($item->status === 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                <a href="{{ route('tugas.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalTugasDestroy{{ $item->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @include('admin/tugas/modal')
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
