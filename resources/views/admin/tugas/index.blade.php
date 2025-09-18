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
                <a href="{{ route('tugas.pdf') }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i>
                    PDF
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tugas</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tugas as $i => $t)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $t->user ? $t->user->nama : 'User tidak ditemukan' }}</td>
                            <td>
                                @if($t->user)
                                    <span class="badge badge-primary">{{ $t->user->email }}</span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $t->tugas }}</td>
                            <td><span class="badge badge-warning">{{ $t->tanggal_mulai }}</span></td>
                            <td><span class="badge badge-danger">{{ $t->tanggal_selesai }}</span></td>
                            <td>
                                @if($t->tugas_file || $t->karyawan_file)
                                    <div class="btn-group">
                                        @if($t->tugas_file)
                                            <a href="{{ asset('storage/'.$t->tugas_file) }}" class="btn btn-sm btn-info" target="_blank" title="File Admin">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @endif
                                        @if($t->karyawan_file)
                                            <a href="{{ asset('storage/'.$t->karyawan_file) }}" class="btn btn-sm btn-success" target="_blank" title="File Karyawan">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
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
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('tugas.show', $t->id) }}" class="btn btn-sm btn-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tugas.edit', $t->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tugas.destroy', $t->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
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
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });
    </script>
@endsection
