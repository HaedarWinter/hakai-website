@extends('layouts.app')

@section('content')
    <!--- page heading --->

    <h1 class="h3 mb-4 text-gray-800 font-weight-bolder">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        DASHBOARD
        {{--        {{$title}}--}}
    </h1>

    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                TOTAL USER</div>
                            <div  class="h5 mb-0 font-weight-bold text-gray-800">{{$totalUser}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Admin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$totalAdmin}}</div>
                            <div class="h6 mb-0 font-weight-normal text-gray-800">
                                {{$persentasiAdmin}}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Karyawan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$totalKaryawan}}</div>
                            <div class="h6 mb-0 font-weight-normal text-gray-800">
                                {{$persentasiKaryawan}}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
