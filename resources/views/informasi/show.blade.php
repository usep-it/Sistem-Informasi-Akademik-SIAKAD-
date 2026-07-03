@extends('layouts.frontend')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4" data-aos="fade-up">
        {{-- Judul --}}
        <div class="card-header bg-gradient-primary text-white py-3 rounded-top-4">
            <h1 class="m-0 fw-bold">{{ $info->judul ?? 'Informasi Akademik' }}</h1>
        </div>

        {{-- Foto --}}
        @if($info->foto)
        <figure class="m-0">
            <img src="{{ url('/informasi_foto/' . $info->foto) }}" class="card-img-top" alt="Foto Informasi"
                 style="max-height:450px; object-fit:cover;">
            <figcaption class="text-center text-muted small mt-1">Foto Informasi</figcaption>
        </figure>
        @endif

        {{-- Isi --}}
        <div class="card-body">
            <p class="card-text fs-5">{{ $info->isi }}</p>
            <small class="text-muted d-block mt-3">Tanggal: {{ date('d M Y', strtotime($info->created_at)) }}</small>
        </div>

        {{-- Kembali --}}
        <div class="card-footer text-end bg-light rounded-bottom-4">
            <a href="{{ url('/') }}" class="btn btn-outline-primary">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection
