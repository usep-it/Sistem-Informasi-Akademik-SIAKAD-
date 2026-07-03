@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Nilai</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ url('home') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item">Nilai</div>
                </div>
            </div>

            {{-- Notifikasi --}}
            @if(session('notif_error'))
                <div class="alert alert-danger text-center">
                    {{ session('notif_error') }}
                </div>
            @endif

            <div class="section-body text-center mt-5">
                <div class="card shadow-sm">
                    <div class="card-body py-5">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                        <h4 class="text-muted mb-3">Belum Ada Data Tahun Ajaran</h4>
                        <p class="text-secondary">
                            Silakan tambahkan data tahun ajaran terlebih dahulu melalui menu <strong>Tahun Ajaran</strong> di dashboard admin.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
