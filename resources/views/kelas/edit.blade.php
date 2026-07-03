@extends('layouts.backend')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit {{ $edit->kelas }} {{ $edit->nama }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="{{ url('kelas') }}">Kelas</a></div>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if (Session::has('notif'))
            <div class="alert alert-primary text-center">
                {!! Session::get('notif') !!}
            </div>
        @endif

        {{-- Error Validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="section-body">
            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <form action="{{ url('kelas/' . $edit->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-body">

                                {{-- Input kelas dan fase --}}
                                <div class="row">
                                    <div class="col-8 col-sm-8">
                                        <label>Kelas</label>
                                        <select name="kelas" class="form-control" required>
                                            <option value="" disabled>-- Pilih Kelas --</option>
                                            @for ($i = 1; $i <= 6; $i++)
                                                <option value="{{ $i }}" {{ old('kelas', $edit->kelas) == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-4 col-sm-4">
                                        <label>Fase</label>
                                        <select name="nama" class="form-control" required>
                                            <option value="" disabled>-- Pilih --</option>
                                            @foreach (['A', 'B', 'C'] as $nama)
                                                <option value="{{ $nama }}" {{ old('nama', $edit->nama) == $nama ? 'selected' : '' }}>
                                                    {{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Input wali kelas dan tahun --}}
                                <div class="row mt-3">
                                    <div class="col-8 col-sm-8">
                                        <label>Wali Kelas</label>
                                        <select name="pegawai_id" class="form-control" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach ($wali as $wl)
                                                <option value="{{ $wl->id }}" {{ old('pegawai_id', $edit->pegawai_id) == $wl->id ? 'selected' : '' }}>
                                                    {{ $wl->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-4 col-sm-4">
                                        <label>Tahun Pelajaran</label>
                                        <select name="tahun_id" class="form-control" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach ($tahun as $thn)
                                                <option value="{{ $thn->id }}" {{ old('tahun_id', $edit->tahun_id) == $thn->id ? 'selected' : '' }}>
                                                    {{ $thn->nama }} — Semester {{ ucfirst($thn->semester) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ url('kelas') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
