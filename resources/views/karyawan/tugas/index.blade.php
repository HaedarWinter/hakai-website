@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user-friends"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header bg-primary d-flex flex-wrap justify-content-between align-items-center py-3">
            <h6 class="mb-0 text-white">Daftar Tugas</h6>
            @if(auth()->user()->is_tugas == true)
                <a href="{{ route('pdf') }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
            @endif
        </div>

        <div class="card-body">
            {{-- Cek jumlah tugas, bukan is_tugas --}}
            @if($tugas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tugas</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Upload File</th>
                            <th>Status Tugas</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tugas as $i => $t)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $t->user->nama }}</td>
                                <td><span class="badge badge-primary">{{ $t->user->email }}</span></td>
                                <td>{{ $t->tugas }}</td>
                                <td><span class="badge badge-warning">{{ $t->tanggal_mulai }}</span></td>
                                <td><span class="badge badge-danger">{{ $t->tanggal_selesai }}</span></td>
                                <td>
                                    @if($t->tugas_file)
                                        <a href="{{ asset('storage/'.$t->tugas_file) }}"
                                           class="btn btn-sm btn-info mb-2" target="_blank">
                                            <i class="fas fa-file"></i> Lihat
                                        </a>
                                    @endif
                                    <form action="{{ route('tugas.upload', $t->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="file" class="form-control-file mb-2" required>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                    </form>
                                </td>
                                {{-- Status di td terpisah --}}
                                <td>
                                    @php
                                        $status = strtolower(trim($t->status));
                                        $statusClass = 'secondary';
                                        if($status == 'pending') $statusClass = 'warning';
                                        elseif($status == 'rejected') $statusClass = 'danger';
                                        elseif($status == 'approved') $statusClass = 'success';
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-danger">
                    Belum Ditugaskan
                </div>
            @endif
        </div>
    </div>
@endsection
