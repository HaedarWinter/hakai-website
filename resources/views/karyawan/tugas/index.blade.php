@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-tasks"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-primary d-flex flex-wrap justify-content-between align-items-center py-3">
            <h6 class="mb-0 text-white">Daftar Tugas Saya</h6>
            @if(auth()->user()->tugas()->count() > 0)
                <a href="{{ route('tugas.pdf') }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
            @endif
        </div>

        <div class="card-body">
            @if($tugas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Tugas</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>File dari Admin</th>
                            <th>Upload File Saya</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tugas as $i => $t)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $t->tugas }}</td>
                                <td><span class="badge badge-warning">{{ $t->tanggal_mulai }}</span></td>
                                <td><span class="badge badge-danger">{{ $t->tanggal_selesai }}</span></td>
                                <td>
                                    @if($t->tugas_file)
                                        <a href="{{ asset('storage/'.$t->tugas_file) }}"
                                           class="btn btn-sm btn-info mb-2" target="_blank">
                                            <i class="fas fa-file"></i> Lihat File
                                        </a>
                                    @else
                                        <span class="text-muted">Tidak ada file</span>
                                    @endif
                                </td>
                                <td>
                                    @if($t->karyawan_file)
                                        <div class="mb-2">
                                            <a href="{{ asset('storage/'.$t->karyawan_file) }}"
                                               class="btn btn-sm btn-success" target="_blank">
                                                <i class="fas fa-file"></i> Lihat File Saya
                                            </a>
                                        </div>
                                    @endif

                                    <form action="{{ route('tugas.upload', $t->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="file" class="form-control-file mb-2"
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-upload"></i> Upload File
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    @php
                                        $status = strtolower(trim($t->status));
                                        $statusClass = 'secondary';
                                        if($status == 'pending') $statusClass = 'warning';
                                        elseif($status == 'submitted') $statusClass = 'info';
                                        elseif($status == 'rejected') $statusClass = 'danger';
                                        elseif($status == 'approved') $statusClass = 'success';
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ ucfirst($status) }}</span>

                                    @if($status == 'rejected' && $t->catatan)
                                        <div class="mt-2">
                                            <small class="text-danger">
                                                <strong>Catatan:</strong> {{ $t->catatan }}
                                            </small>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i> Anda belum memiliki tugas
                </div>
            @endif
        </div>
    </div>
@endsection
