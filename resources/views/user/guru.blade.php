@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Akun GTK</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Akun GTK</div>
                </div>
            </div>

            @if (session('notif'))
                <div class="alert alert-primary text-center">
                    {!! session('notif') !!}
                </div>
            @endif
            
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
                <div class="card-header d-flex justify-content-between">
                    <h4>Daftar Akun GTK</h4>
                    <div>
                        <a href="{{ route('user.export_guru') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                        <a href="#" data-toggle="modal" data-target="#modalTambahGuru" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Tambah Akun</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username (Email)</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($aguru as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->pegawai?->nama ?? $item->name }}</td>
                                        <td>{{ $item->username }}</td>
                                        <td class="text-center" nowrap>
    <a href="/user/reset-password/{{ $item->uuid }}/"
       class="btn btn-warning btn-sm"
       onclick="return confirm('Reset password user {{ $item->username }} menjadi default?')">
        <i class="fa fa-key"></i> Reset
    </a>

    <form action="{{ route('user.destroy', $item->uuid) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Anda yakin ingin menghapus akun {{ $item->username }}?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="btn btn-danger btn-sm"
                title="Hapus Akun">
            <i class="fa fa-trash"></i> Hapus
        </button>
    </form>
</td>
</tr>
@empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada akun guru yang dibuat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<div class="modal fade" id="modalTambahGuru" tabindex="-1" role="dialog" aria-labelledby="modalTambahGuruLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahGuruLabel"><i class="fas fa-user-plus mr-2"></i> Buat Akun Guru Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.store.guru') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        Akun akan dibuat menggunakan <b>Email</b> sebagai username. Pastikan GTK yang dipilih memiliki email yang valid.
                    </div>
                    <div class="form-group">
    <label>Pilih Guru (Hanya yang belum punya akun & punya email)</label>
    <select name="pegawai_id" class="form-control @error('pegawai_id') is-invalid @enderror" required>
        <option value="" selected disabled>-- Pilih Guru --</option>
        @foreach ($guru as $gr)
            <option value="{{ $gr->id }}" {{ old('pegawai_id') == $gr->id ? 'selected' : '' }}>
                {{ $gr->nama }} (Email: {{ $gr->email }})
            </option>
        @endforeach
    </select>
    @error('pegawai_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Password</label>
    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        @if ($errors->any())
            $('#modalTambahGuru').modal('show');
        @endif
    });
</script>
@endpush

