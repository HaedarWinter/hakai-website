@php use http\Client\Curl\User; @endphp
@extends('layouts.app')
@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user-friends"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-end align-items-center py-3">
            <div>
                @if(auth()->user()->is_tugas == true)
                    <a href="#" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i>
                        PDF
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">

            @if(auth()->user()->is_tugas == true)
                <div class="row">
                    <div class="col-6">
                        Nama
                    </div>
                    <div class="col-6">
                        : {{ $tugas->user->nama }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        Email
                    </div>
                    <div class="col-6">
                        :
                        <span class="badge badge-primary">
                         {{ $tugas->user->email }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        Tugas
                    </div>
                    <div class="col-6">
                        : {{ $tugas->tugas}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        Tanggal Mulai
                    </div>
                    <div class="col-6">
                        :
                        <span class="badge badge-warning">
                            {{ $tugas->tanggal_mulai }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        Tanggal Selesai
                    </div>
                    <div class="col-6">
                        :
                        <span class="badge badge-danger">
                            {{ $tugas->tanggal_selesai }}
                        </span>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-6">
                         <span class="badge badge-danger">
                                Belum Ditugaskan
                         </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
