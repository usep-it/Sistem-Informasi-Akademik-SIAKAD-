@extends('layouts.backend')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Pengaturan Pengumuman Kelulusan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('graduation-settings.index') }}">Pengaturan Kelulusan</a></div>
                <div class="breadcrumb-item">Tambah</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-header">
                            <h4>Form Pengaturan Baru</h4>
                        </div>
                        <form action="{{ route('graduation-settings.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                {{-- Waktu Buka --}}
                                <div class="form-group mb-4">
                                    <label for="waktu_buka">Waktu Buka <span class="text-danger">*</span></label>
                                    <input 
                                        type="datetime-local" 
                                        id="waktu_buka" 
                                        name="waktu_buka" 
                                        class="form-control @error('waktu_buka') is-invalid @enderror"
                                        value="{{ old('waktu_buka') }}"
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
                                        value="{{ old('waktu_tutup') }}"
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
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
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
                                        placeholder="Contoh: Pengumuman Kelulusan 2025/2026"
                                    >{{ old('keterangan') }}</textarea>
                                    <small class="form-text text-muted">Keterangan tambahan untuk keperluan dokumentasi</small>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer bg-whitesmoke">
                                <a href="{{ route('graduation-settings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fas fa-save"></i> Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Konversi input datetime-local ke format yang dapat dibaca
    document.addEventListener('DOMContentLoaded', function() {
        const waktuBukaInput = document.getElementById('waktu_buka');
        const waktuTutupInput = document.getElementById('waktu_tutup');

        // Set default values jika kosong
        if (!waktuBukaInput.value) {
            const now = new Date();
            now.setFullYear(2026, 4, 1); // May 1, 2026
            now.setHours(0, 0);
            waktuBukaInput.value = now.toISOString().slice(0, 16);
        }

        if (!waktuTutupInput.value) {
            const now = new Date();
            now.setFullYear(2026, 5, 30); // June 30, 2026
            now.setHours(23, 59);
            waktuTutupInput.value = now.toISOString().slice(0, 16);
        }
    });
</script>
@endsection
