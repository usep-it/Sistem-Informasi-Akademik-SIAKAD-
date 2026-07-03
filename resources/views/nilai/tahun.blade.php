@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Nilai</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Nilai</div>
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
                <div class="card">
                    <div class="card-header">
                        <h4>Pilih Tahun Ajaran dan Semester</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr class="text-center">
                                        <th width="6%">No</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Semester</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tahun as $item)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $item->semester == 'ganjil' ? 'info' : 'success' }}">
                                                    Semester {{ ucfirst($item->semester) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ url('nilai/tahun/'.$item->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-search"></i> Pilih
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                Belum ada data tahun ajaran atau semester.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
