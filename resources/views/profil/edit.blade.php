@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Profil</h1>
            </div>

            {{-- Notifikasi sukses --}}
            @if (session('notif'))
                <div class="alert alert-success text-center">{{ session('notif') }}</div>
            @endif

            {{-- Notifikasi error --}}
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
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>Perbarui Informasi Akun</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- FOTO PROFIL --}}
                            <div class="form-group text-center">
                                <img id="previewFoto"
                                     src="{{ $user->foto ? asset('foto_user/'.$user->foto) : asset('update/img/avatar-1.png') }}"
                                     alt="Foto Profil"
                                     class="rounded-circle mb-3"
                                     width="120" height="120"
                                     style="object-fit: cover;">
                                <div>
                                    <label for="foto" class="font-weight-bold">Ubah Foto Profil</label>
                                    <input type="file"
                                           name="foto"
                                           id="foto"
                                           class="form-control @error('foto') is-invalid @enderror"
                                           accept="image/*"
                                           onchange="previewImage(event)">
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            {{-- PASSWORD BARU (OPSIONAL) --}}
                            <div class="form-group">
                                <label for="password" class="font-weight-bold">Password Baru</label>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       value="" {{-- pastikan kosong --}}
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Kosongkan jika tidak ingin ganti password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="font-weight-bold">Konfirmasi Password Baru</label>
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       value="" {{-- pastikan kosong --}}
                                       class="form-control"
                                       placeholder="Ulangi password baru">
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

{{-- SCRIPT PREVIEW FOTO --}}
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        // Update preview di halaman edit
        document.getElementById('previewFoto').src = reader.result;

        // Update foto navbar langsung juga tanpa reload
        const navFoto = document.querySelector('.navbar .nav-link img');
        if (navFoto) {
            navFoto.src = reader.result;
        }
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
