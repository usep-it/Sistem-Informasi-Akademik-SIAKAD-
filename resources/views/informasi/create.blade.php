@extends('layouts.backend')

@push('styles')
{{-- Load Summernote CSS --}}
<link rel="stylesheet" href="{{ url('update/modules/summernote/summernote-bs4.css') }}">
<style>
    /* Styling Identik dengan Foto e17fd1 */
    .card-news {
        border-top: 2px solid #47c363 !important; /* Warna Hijau Word-style */
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .form-label-news {
        font-weight: 600;
        color: #34395e;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .note-editor.note-frame {
        border: 1px solid #e4e6fc !important;
        border-radius: 4px;
    }
    .note-toolbar {
        background: #f9f9f9 !important;
        border-bottom: 1px solid #e4e6fc !important;
    }
    /* Custom button Pilih File */
    .custom-file-input-news {
        display: none;
    }
    .btn-upload-news {
        background-color: #007bff;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        display: inline-block;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-upload-news:hover { background-color: #0056b3; }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between">
                <h1 style="font-size: 22px;">Tulis Berita Baru</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ url('home') }}" class="text-success">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('informasi.index') }}" class="text-success">Berita</a></div>
                    <div class="breadcrumb-item">Tulis Berita</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card card-news">
                    <form action="{{ route('informasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body p-4 p-md-5">
                            {{-- Baris Judul --}}
                            <div class="form-group mb-4">
                                <label class="form-label-news">Judul</label>
                                <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" 
                                       placeholder="Masukkan judul berita di sini..." value="{{ old('judul') }}" required>
                                @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Baris Foto (Hanya Foto saja, Penulis dihapus) --}}
                            <div class="form-group mb-4">
                                <label class="form-label-news d-block">Foto Utama</label>
                                <label for="foto-upload" class="btn-upload-news shadow-sm">
                                    <i class="fas fa-image mr-2"></i> Pilih File
                                </label>
                                <input type="file" id="foto-upload" name="foto" class="custom-file-input-news @error('foto') is-invalid @enderror" accept="image/*" required onchange="previewName(this)">
                                <span id="file-name" class="ml-3 text-muted">Belum ada file yang dipilih</span>
                                @error('foto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Baris Deskripsi (Summernote Editor) --}}
                            <div class="form-group">
                                <label class="form-label-news">Deskripsi</label>
                                <textarea name="isi" id="summernote" class="summernote @error('isi') is-invalid @enderror" required>{{ old('isi') }}</textarea>
                                @error('isi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Tombol Simpan Center --}}
                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-success px-5 py-2 font-weight-bold shadow-sm" style="border-radius: 8px;">
                                    Simpan Data Berita
                                </button>
                                <a href="{{ route('informasi.index') }}" class="btn btn-light ml-2 px-4 py-2" style="border-radius: 8px;">Batal</a>
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
        // Inisialisasi Editor seperti Microsoft Word
        $('#summernote').summernote({
            placeholder: 'Tuliskan isi berita secara lengkap di sini...',
            tabsize: 2,
            height: 400,
            lang: 'id-ID',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            // Pastikan font default terlihat profesional
            fontNames: ['Open Sans', 'Arial', 'Inter', 'Poppins']
        });
    });

    // Menampilkan nama file saat dipilih
    function previewName(input) {
        let fileName = input.files[0].name;
        document.getElementById('file-name').innerText = fileName;
    }
</script>
@endpush