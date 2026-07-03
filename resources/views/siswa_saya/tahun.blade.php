@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Siswa Saya</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Siswa Saya</div>
                </div>
            </div>

            {{-- Notifikasi --}}
            @if (session('notif'))
                <div class="alert alert-primary text-center shadow-sm">
                    {!! session('notif') !!}
                </div>
            @endif
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
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4>Pilih Tahun Ajaran</h4>
                            </div>
                            <div class="card-body">
                                {{-- Jika belum ada data tahun --}}
                                @if($tahun->isEmpty())
                                    <div class="alert alert-warning text-center">
                                        Belum ada data Tahun Ajaran yang tersedia.
                                    </div>
                                @else
                                    {{-- Dropdown Tahun --}}
                                    <div class="form-group">
                                        <label for="tahun_id">
                                            Pilih Tahun Ajaran untuk melihat daftar siswa:
                                        </label>
                                        <select id="tahun_id" class="form-control select2">
                                            <option value="">-- Silakan Pilih --</option>
                                            @foreach ($tahun as $item)
                                                <option value="{{ $item->id }}" 
                                                    {{ $tahunAktif && $tahunAktif->id == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama }} — Semester {{ ucfirst($item->semester) }}
                                                    @if($item->status == 'Aktif') (Aktif) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Tombol --}}
                                    <div class="text-right">
                                        <button class="btn btn-primary" onclick="lihatSiswa()">
                                            <i class="fa fa-search"></i> Tampilkan Daftar Siswa
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Info Tahun Aktif --}}
                            @if($tahunAktif)
                                <div class="card-footer text-center bg-light">
                                    <small>
                                        Tahun Aktif Saat Ini:
                                        <strong>{{ $tahunAktif->nama }}</strong> —
                                        Semester <strong>{{ ucfirst($tahunAktif->semester) }}</strong>
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script>
    function lihatSiswa() {
        const tahunId = document.getElementById('tahun_id').value;

        if (tahunId) {
            window.location.href = '/siswa-saya/tahun/' + tahunId;
        } else {
            alert('Silakan pilih Tahun Ajaran terlebih dahulu.');
        }
    }
</script>
@endpush
