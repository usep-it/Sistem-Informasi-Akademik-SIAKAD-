@extends('layouts.backend')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Jadwal Pelajaran</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal</a></div>
                <div class="breadcrumb-item active">Edit</div>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if (session('notif'))
            <div class="alert alert-success text-center">
                {!! session('notif') !!}
            </div>
        @endif

        {{-- Validasi Error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $tahunAktif = \App\Models\Tahun::where('status', 'Aktif')->first();
        @endphp

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-10 offset-md-1">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-edit"></i> Form Edit Jadwal</h4>
                        </div>

                        <form action="{{ route('jadwal.update', $edit->id) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <div class="form-row mb-3">
                                    <div class="col-md-4">
                                        <label><b>Kelas</b></label>
                                        <select name="kelas_id" class="form-control" required>
                                            @foreach ($kelas as $kls)
                                                <option value="{{ $kls->id }}" {{ $edit->kelas_id == $kls->id ? 'selected' : '' }}>
                                                    {{ $kls->kelas }} {{ $kls->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label><b>Mata Pelajaran</b></label>
                                        <select name="mapel_id" class="form-control" required>
                                            @foreach ($mapel as $mpl)
                                                <option value="{{ $mpl->id }}" {{ $edit->mapel_id == $mpl->id ? 'selected' : '' }}>
                                                    {{ $mpl->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label><b>Tahun Ajaran & Semester</b></label>
                                        <input type="text" class="form-control bg-light" 
                                               value="{{ $tahunAktif ? $tahunAktif->nama . ' - Semester ' . ucfirst($tahunAktif->semester) : 'Belum ada tahun aktif' }}" 
                                               readonly>
                                        <input type="hidden" name="tahun_id" value="{{ $tahunAktif->id ?? $edit->tahun_id }}">
                                    </div>
                                </div>

                                <div class="form-row mb-3">
                                    <div class="col-md-4">
                                        <label><b>Hari</b></label>
                                        <select name="hari_id" class="form-control" required>
                                            @foreach ($hari as $hr)
                                                <option value="{{ $hr->id }}" {{ $edit->hari_id == $hr->id ? 'selected' : '' }}>
                                                    {{ $hr->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label><b>Jam Mulai</b></label>
                                        <input type="time" name="jam_mulai" class="form-control"
                                               value="{{ \Carbon\Carbon::parse($edit->jam_mulai)->format('H:i') }}" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label><b>Jam Selesai</b></label>
                                        <input type="time" name="jam_selesai" class="form-control"
                                               value="{{ \Carbon\Carbon::parse($edit->jam_selesai)->format('H:i') }}" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label><b>Guru Pengajar</b></label>
                                        <select name="pegawai_id" class="form-control" required>
                                            @foreach ($guru as $gr)
                                                <option value="{{ $gr->id }}" {{ $edit->pegawai_id == $gr->id ? 'selected' : '' }}>
                                                    {{ $gr->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-right">
                                <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
