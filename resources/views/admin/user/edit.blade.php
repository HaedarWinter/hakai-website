@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-edit"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <!-- Card Header -->
        <div class="card-header bg-warning d-flex flex-wrap py-3">
            <div class="mb-1 mr-2">
                <a href="{{ route('user.index') }}" class="btn btn-sm  border-5 btn-warning btn-light">
                    <i class="fas fa-arrow-circle-left mr-1"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <form action="{{ route('user.update', $user -> id) }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Nama -->
                    <div class="col-md-6 col-12 mb-3">
                        <label><span class="text-danger">*</span> Nama :</label>
                        <input type="text" name="nama" id="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Masukkan nama" value="{{$user -> nama}}">
                        @error('nama')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 col-12 mb-3">
                        <label for="email"><span class="text-danger">*</span> Email :</label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="Masukkan email" value="{{$user -> email}}">
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Jabatan -->
                    <div class="col-12 mb-3">
                        <label for="jabatan"><span class="text-danger">*</span> Jabatan :</label>
                        <select name="jabatan" id="jabatan"
                                class="form-control @error('jabatan') is-invalid @enderror" required>
                            <option disabled>-- Pilih Jabatan --</option>
                            <option value="Admin" {{ $user -> jabatan == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Karyawan" {{ $user -> jabatan == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                        </select>
                        @error('jabatan')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="col-md-6 col-12 mb-3">
                        <label for="password"><span class="text-danger">*</span> Password :</label>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Kosongkan jika tidak ingin mengubah password">
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="col-md-6 col-12 mb-3">
                        <label for="password_confirmation">Konfirmasi Password :</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               placeholder="Kosongkan jika tidak ingin mengubah password">
                        @error('password_confirmation')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <!-- Save Button -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-warning btn-block">
                        <i class="fas fa-edit mr-2"></i> Edit Data User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
