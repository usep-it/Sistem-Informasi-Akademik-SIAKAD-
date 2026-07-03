@extends('layouts.backend')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Nilai {{ $edit->siswa->nama }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ url('siswa') }}">Siswa</a></div>
                </div>
            </div>
            @if (\Session::has('notif'))
                <div class="alert alert-primary" align="center">
                    {!! \Session::get('notif') !!}
                </div>
            @endif
            <!-- error -->
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- end error -->
            <div class="section-body">

                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <form action="{{ route('nilai.update', $edit->uuid) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card-body">
        <div class="form-row">
            <div class="col-6 col-sm-6">
                <label>Siswa</label>
                <input type="text" class="form-control" value="{{ $edit->siswa->nama }}" disabled>
            </div>
            <div class="col-6 col-sm-6">
                <label>Nilai</label>
                <input type="number" name="nilai" class="form-control" value="{{ $edit->nilai }}" required>
            </div>
        </div>

        <div class="form-group mt-3 text-right">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-warning">Update</button>
        </div>
    </div>
</form>

                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>
@endsection
