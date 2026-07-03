@extends('layouts.backend')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Siswa</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('siswa.index') }}">Data Siswa</a></div>
                    <div class="breadcrumb-item">Edit Siswa</div>
                </div>
            </div>

            @if (session('notif'))
                <div class="alert alert-primary">
                    {!! session('notif') !!}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Formulir Edit Data: {{ $edit->nama }}</h4>
                    </div>
                    <div class="card-body">
                        {{-- PERBAIKAN: Form action menggunakan named route dan method PUT --}}
                        <form action="{{ route('siswa.update', $edit->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $edit->nama) }}" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Tempat Lahir</label>
                                        <input type="text" name="tempat" class="form-control @error('tempat') is-invalid @enderror" value="{{ old('tempat', $edit->tempat) }}" required>
                                        @error('tempat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                     <div class="form-group">
                                        <label>Tanggal Lahir</label>
                                        <input type="date" name="ttl" class="form-control @error('ttl') is-invalid @enderror" value="{{ old('ttl', $edit->ttl) }}" required>
                                        @error('ttl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select name="jk" class="form-control @error('jk') is-invalid @enderror" required>
                                            <option value="" disabled>-- Pilih --</option>
                                            <option value="Laki-Laki" {{ old('jk', $edit->jk) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                            <option value="Perempuan" {{ old('jk', $edit->jk) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>NIS</label>
                                        <input type="number" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis', $edit->nis) }}" required>
                                        @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label>NISN</label>
                                        <input type="number" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $edit->nisn) }}" required>
                                        @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label>No HP (Orang Tua/Wali)</label>
                                        <input type="number" name="hp" class="form-control @error('hp') is-invalid @enderror" value="{{ old('hp', $edit->hp) }}">
                                        @error('hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                             <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $edit->alamat) }}</textarea>
                                 @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="card-footer text-right">
                                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Update Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
