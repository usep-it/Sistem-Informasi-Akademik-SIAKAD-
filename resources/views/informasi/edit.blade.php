@extends('layouts.backend')

@push('styles')
{{-- Load Summernote CSS --}}
<link rel="stylesheet" href="{{ url('update/modules/summernote/summernote-bs4.css') }}">
<style>
    /* Layout Wrapper */
    .main-content { overflow: visible !important; }

    /* Styling Card Berita (Gaya Word) */
    .card-news {
        border-top: 3px solid #47c363 !important; /* Hijau sesuai screenshot create */
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        border-left: none;
        border-right: none;
        border-bottom: none;
    }
    
    .form-label-news {
        font-weight: 700;
        color: #495057;
        margin-bottom: 10px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .news-input {
        border: 1px solid #e4e6fc;
        border-radius: 8px;
        padding: 12px 15px;
        transition: 0.3s;
    }
    .news-input:focus {
        border-color: #47c363;
        box-shadow: 0 0 0 4px rgba(71, 195, 99, 0.1);
    }

    /* Summernote Adjustment */
    .note-editor.note-frame {
        border: 1px solid #e4e6fc !important;
        border-radius: 8px;
        overflow: hidden;
    }
    .note-toolbar {
        background: #fcfcfd !important;
        border-bottom: 1px solid #e4e6fc !important;
        padding: 10px !important;
    }

    /* Foto Preview Styling */
    .current-photo-box {
        background: #f8faff;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #eef2f7;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .img-current {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .btn-select-file {
        background-color: #007bff;
        color: white !important;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 700;
        font-size: 13px;
        border: none;
        transition: 0.3s;
    }
    .btn-select-file:hover { background-color: #0056b3; }
    #foto-upload { display: none; }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header justify-content-between">
                <h1 style="font-size: 22px;">Edit Informasi / Berita</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('informasi.index') }}">Berita</a></div>
                    <div class="breadcrumb-item">Edit Berita</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card card-news">
                    {{-- Gunakan $edit->id atau $edit->uuid sesuai kebutuhan database Anda --}}
                    <form action="{{ route('informasi.update', $edit->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body p-4 p-md-5">
                            
                            {{-- Judul Berita --}}
                            <div class="form-group mb-4">
                                <label class="form-label-news">Judul Berita</label>
                                <input type="text" name="judul" class="form-control news-input @error('judul') is-invalid @enderror" 
                                       placeholder="Ketik judul berita..." value="{{ old('judul', $edit->judul) }}" required>
                                @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Foto Saat Ini & Input Ganti Foto --}}
                            <div class="form-group mb-4">
                                <label class="form-label-news">Foto Berita</label>
                                <div class="current-photo-box shadow-sm mb-3">
                                    <div class="text-center">
                                        <img src="{{ url('/informasi_foto/' . $edit->foto) }}" alt="Current Photo" class="img-current border">
                                        <small class="d-block text-muted mt-1">Foto Saat Ini</small>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label for="foto-upload" class="btn-select-file m-0">
                                            <i class="fas fa-sync-alt mr-2"></i> Ganti Foto Baru
                                        </label>
                                        <input type="file" id="foto-upload" name="foto" accept="image/*" onchange="updateFileName(this)">
                                        <p id="file-name-display" class="text-muted small mt-2 mb-0">Biarkan kosong jika tidak ingin mengubah foto.</p>
                                    </div>
                                </div>
                                @error('foto') <small class="text-danger font-weight-bold d-block">{{ $message }}</small> @enderror
                            </div>

                            {{-- Deskripsi (Word-style Editor) --}}
                            <div class="form-group">
                                <label class="form-label-news">Isi Berita Lengkap</label>
                                <textarea name="isi" id="summernote" class="summernote @error('isi') is-invalid @enderror" required>{{ old('isi', $edit->isi) }}</textarea>
                                @error('isi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Button Aksi --}}
                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-warning btn-lg px-5 py-2 shadow-sm" style="border-radius: 50px; font-weight: 800; font-size: 15px;">
                                    <i class="fas fa-save mr-2"></i> UPDATE PERUBAHAN
                                </button>
                                <div class="mt-3">
                                    <a href="{{ route('informasi.index') }}" class="text-muted small font-weight-bold">Batal dan Kembali</a>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
{{-- Load Summernote JS --}}
<script src="{{ url('update/modules/summernote/summernote-bs4.js') }}"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Summernote (Identik dengan halaman Create)
        $('#summernote').summernote({
            placeholder: 'Edit isi berita di sini...',
            tabsize: 2,
            height: 450,
            lang: 'id-ID',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    // Update Text Nama File saat dipilih
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-name-display').innerText = "File terpilih: " + input.files[0].name;
            document.getElementById('file-name-display').classList.replace('text-muted', 'text-success');
            document.getElementById('file-name-display').style.fontWeight = 'bold';
        }
    }
</script>
@endpush