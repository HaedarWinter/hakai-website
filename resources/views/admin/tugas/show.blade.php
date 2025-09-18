@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-clipboard-check"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-info d-flex flex-wrap justify-content-between align-items-center py-3">
            <h6 class="mb-0 text-white">Detail Tugas</h6>
            <div>
                <a href="{{ route('tugas.index') }}" class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <td width="30%"><strong>Nama Karyawan</strong></td>
                            <td>{{ $tugas->user ? $tugas->user->nama : 'User tidak ditemukan' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>{{ $tugas->user ? $tugas->user->email : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tugas</strong></td>
                            <td>{{ $tugas->tugas }}</td>
                        </tr>
                        <tr>
                            <td><strong>Periode</strong></td>
                            <td>{{ $tugas->tanggal_mulai }} s/d {{ $tugas->tanggal_selesai }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>
                                @php
                                    $status = strtolower(trim($tugas->status));
                                    $statusClass = 'secondary';
                                    if($status == 'pending') $statusClass = 'warning';
                                    elseif($status == 'submitted') $statusClass = 'info';
                                    elseif($status == 'rejected') $statusClass = 'danger';
                                    elseif($status == 'approved') $statusClass = 'success';
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">File dari Admin</h6>
                        </div>
                        <div class="card-body">
                            @if($tugas->tugas_file)
                                <a href="{{ asset('storage/'.$tugas->tugas_file) }}"
                                   class="btn btn-info btn-sm" target="_blank">
                                    <i class="fas fa-file"></i> Lihat File
                                </a>
                            @else
                                <p class="text-muted">Tidak ada file</p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">File dari Karyawan</h6>
                        </div>
                        <div class="card-body">
                            @if($tugas->karyawan_file)
                                <a href="{{ asset('storage/'.$tugas->karyawan_file) }}"
                                   class="btn btn-success btn-sm mb-2" target="_blank">
                                    <i class="fas fa-file"></i> Lihat File Karyawan
                                </a>

                                @if($tugas->status != 'approved')
                                    <div class="mt-3">
                                        <form action="{{ route('tugas.approve', $tugas->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </div>
                                @endif
                            @else
                                <p class="text-muted">Karyawan belum mengupload file</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($tugas->catatan)
                <div class="alert alert-warning mt-3">
                    <strong>Catatan:</strong> {{ $tugas->catatan }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tolak -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Tugas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('tugas.reject', $tugas->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="catatan">Catatan Penolakan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Tugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
