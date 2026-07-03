@extends('layouts.backend')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Pengaturan Pengumuman Kelulusan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('graduation-settings.index') }}">Pengaturan Kelulusan</a></div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Pengaturan</h4>
                        </div>
                        <form action="{{ route('graduation-settings.update', $graduationSetting) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                {{-- Waktu Buka --}}
                                <div class="form-group mb-4">
                                    <label for="waktu_buka">Waktu Buka <span class="text-danger">*</span></label>
                                    <input 
                                        type="datetime-local" 
                                        id="waktu_buka" 
                                        name="waktu_buka" 
                                        class="form-control @error('waktu_buka') is-invalid @enderror"
                                        value="{{ old('waktu_buka', $graduationSetting->waktu_buka->format('Y-m-d\TH:i')) }}"
                                        required
                                    >
                                    <small class="form-text text-muted">Kapan pengumuman kelulusan akan dibuka</small>
                                    @error('waktu_buka')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Waktu Tutup --}}
                                <div class="form-group mb-4">
                                    <label for="waktu_tutup">Waktu Tutup <span class="text-danger">*</span></label>
                                    <input 
                                        type="datetime-local" 
                                        id="waktu_tutup" 
                                        name="waktu_tutup" 
                                        class="form-control @error('waktu_tutup') is-invalid @enderror"
                                        value="{{ old('waktu_tutup', $graduationSetting->waktu_tutup->format('Y-m-d\TH:i')) }}"
                                        required
                                    >
                                    <small class="form-text text-muted">Kapan pengumuman kelulusan akan ditutup</small>
                                    @error('waktu_tutup')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="form-group mb-4">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="active" {{ old('status', $graduationSetting->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status', $graduationSetting->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    <small class="form-text text-muted">Status aktif akan digunakan di halaman cek kelulusan</small>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Keterangan --}}
                                <div class="form-group mb-4">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea 
                                        id="keterangan" 
                                        name="keterangan" 
                                        class="form-control @error('keterangan') is-invalid @enderror" 
                                        rows="3"
                                    >{{ old('keterangan', $graduationSetting->keterangan) }}</textarea>
                                    <small class="form-text text-muted">Keterangan tambahan untuk keperluan dokumentasi</small>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Info Audit --}}
                                <div class="alert alert-info" role="alert">
                                    <small>
                                        <strong>Dibuat:</strong> {{ $graduationSetting->created_at->format('d M Y, H:i') }} <br>
                                        <strong>Diubah:</strong> {{ $graduationSetting->updated_at->format('d M Y, H:i') }}
                                    </small>
                                </div>
                            </div>

                            <div class="card-footer bg-whitesmoke">
                                <a href="{{ route('graduation-settings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fas fa-save"></i> Simpan Perubahan
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
