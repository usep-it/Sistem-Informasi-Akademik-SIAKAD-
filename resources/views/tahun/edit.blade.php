@extends('layouts.backend')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Tahun Pelajaran</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="{{ route('tahun.index') }}">Tahun Pelajaran</a></div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if (session('notif'))
            <div class="alert alert-primary text-center">
                {!! session('notif') !!}
            </div>
        @endif

        {{-- Error --}}
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
            <div class="row mt-sm-4 justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4>Edit Data Tahun Pelajaran</h4>
                        </div>
                        <form action="{{ route('tahun.update', $edit->id) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nama">Nama Tahun Pelajaran</label>
                                    <input type="text" id="nama" name="nama"
                                        class="form-control @error('nama') is-invalid @enderror"
                                        value="{{ old('nama', $edit->nama) }}"
                                        placeholder="Contoh: 2025/2026" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="semester">Semester</label>
                                    <select name="semester" id="semester"
                                        class="form-control @error('semester') is-invalid @enderror" required>
                                        <option value="">-- Pilih Semester --</option>
                                        <option value="Ganjil" {{ $edit->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                        <option value="Genap" {{ $edit->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('tahun.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
