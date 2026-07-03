@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data GTK: {{ $edit->nama }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('pegawai.index') }}">Data GTK</a></div>
                    <div class="breadcrumb-item active">Edit</div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <p><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Formulir Edit Data GTK</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('pegawai.update', $edit->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Nama & Jenis Kelamin --}}
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $edit->nama) }}" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jk" class="form-control @error('jk') is-invalid @enderror" required>
                                    <option value="Laki-Laki" {{ old('jk', $edit->jk) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ old('jk', $edit->jk) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Tempat & Tanggal Lahir --}}
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $edit->tempat_lahir) }}" required>
                                @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                               <label>Tanggal Lahir</label>
                               <input type="date" name="ttl" class="form-control @error('ttl') is-invalid @enderror" value="{{ old('ttl', $edit->ttl) }}" required>
                               @error('ttl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- NIP & Status Kepegawaian --}}
                        <div class="row">
                            <div class="col-md-6 form-group">
                               <label>NIP</label>
                               <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $edit->nip) }}">
                                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            </div>
                            <div class="col-md-6 form-group">
                                <label>Status Kepegawaian</label>
                                <select name="status_kepegawaian" class="form-control @error('status_kepegawaian') is-invalid @enderror" required>
                                     <option value="PNS" {{ old('status_kepegawaian', $edit->status_kepegawaian) == 'PNS' ? 'selected' : '' }}>PNS</option>
                                     <option value="PPPK" {{ old('status_kepegawaian', $edit->status_kepegawaian) == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                     <option value="Honorer" {{ old('status_kepegawaian', $edit->status_kepegawaian) == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                                 </select>
                                @error('status_kepegawaian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Jabatan --}}
                        <div class="form-group">
                            <label>Jabatan</label>
                            <select name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" required>
                                <option value="Kepala Sekolah" {{ old('jabatan', $edit->jabatan) == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                <option value="Guru Kelas" {{ old('jabatan', $edit->jabatan) == 'Guru Kelas' ? 'selected' : '' }}>Guru Kelas</option>
                                <option value="Guru Mapel" {{ old('jabatan', $edit->jabatan) == 'Guru Mapel' ? 'selected' : '' }}>Guru Mapel</option>
                                <option value="Tenaga Administrasi" {{ old('jabatan', $edit->jabatan) == 'Tenaga Administrasi' ? 'selected' : '' }}>Tenaga Administrasi</option>
                            </select>
                             @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- NIK, NUPTK, Email --}}
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $edit->nik) }}">
                                <small class="text-muted">Opsional, 16 digit.</small>
                                @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label>NUPTK</label>
                                <input type="text" name="nuptk" class="form-control @error('nuptk') is-invalid @enderror" value="{{ old('nuptk', $edit->nuptk) }}">
                                <small class="text-muted">Opsional.</small>
                                @error('nuptk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $edit->email) }}">
                                <small class="text-muted">Opsional.</small>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $edit->alamat) }}</textarea>
                            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Foto --}}
                        <div class="form-group">
                            <label>Ganti Foto (Opsional)</label>
                            @if($edit->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('foto_pegawai/' . $edit->foto) }}" alt="Foto Saat Ini" width="150" class="img-thumbnail">
                                </div>
                            @endif
                            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                            @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="card-footer text-right bg-whitesmoke">
                            <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
</main>
@endsection
