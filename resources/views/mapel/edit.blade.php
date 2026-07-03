@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas fa-edit mr-2"></i>Edit Mata Pelajaran</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('mapel.index') }}">Mata Pelajaran</a></div>
                    <div class="breadcrumb-item">Edit</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="card shadow-sm border-top border-warning" style="border-top-width: 3px !important;">
                            <form action="{{ route('mapel.update', $edit->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body p-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nama Lengkap Mata Pelajaran <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama" value="{{ $edit->nama }}" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Singkatan</label>
                                        <input type="text" class="form-control" name="singkatan" value="{{ $edit->singkatan }}" placeholder="Contoh: PAI" maxlength="15">
                                        <small class="text-muted mt-2 d-block">Maksimal 15 karakter. Contoh: MTK, B. Ind, PAI.</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-whitesmoke text-right">
                                    <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-warning shadow-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Update Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection