@extends('layouts.app')
@section('title', 'Pengaturan Aplikasi')
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Pengaturan Aplikasi" />

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="header-title">Pengaturan Aplikasi</h4>
                            <p class="text-muted mb-0">
                                Informasi dan pengaturan umum aplikasi sekolah
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('settings.edit') }}" class="btn btn-primary">
                                <i class="ri-edit-line me-1"></i> Edit Pengaturan
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($setting)
                            <div class="row">
                                <!-- Logo Preview -->
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <h5 class="mb-3">Logo Sekolah</h5>
                                        <div class="border rounded p-3 bg-light">
                                            <img src="{{ asset('logo.png') }}" alt="Logo Sekolah"
                                                 class="img-fluid" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Settings Info -->
                                <div class="col-md-8">
                                    <h5 class="text-primary mb-3">
                                        <i class="ri-information-line me-2"></i>Informasi Aplikasi
                                    </h5>

                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td width="30%" class="fw-semibold">Nama Aplikasi:</td>
                                                    <td>{{ $setting->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Deskripsi:</td>
                                                    <td>{{ $setting->description }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">URL:</td>
                                                    <td>
                                                        <a href="{{ $setting->url }}" target="_blank">
                                                            {{ $setting->url }} <i class="ri-external-link-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Nama Sekolah:</td>
                                                    <td>{{ $setting->nama_sekolah }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Alamat Sekolah:</td>
                                                    <td>{{ $setting->alamat_sekolah }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Terakhir Diperbarui:</td>
                                                    <td>{{ \Carbon\Carbon::parse($setting->updated_at)->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning" role="alert">
                                <i class="ri-alert-line me-2"></i>
                                Pengaturan aplikasi belum dikonfigurasi. Silakan klik tombol "Edit Pengaturan" untuk mengatur.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Handle flash messages
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: {!! json_encode(session('success')) !!}
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: {!! json_encode(session('error')) !!}
            });
        @endif
    </script>
@endpush
