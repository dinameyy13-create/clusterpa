@extends('layouts.app')

@section('title', 'Rekomendasi Menu AI')
@section('page-title', 'Rekomendasi Menu AI')
@section('breadcrumb', 'Rekomendasi Menu AI')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    #ai-formatted-content ul {
        list-style-type: none;
        padding-left: 10px;
    }
    #ai-formatted-content ul li::before {
        content: "•";
        color: #2563eb;
        font-weight: bold;
        display: inline-block;
        width: 1em;
        margin-left: -1em;
    }
    .ai-response-card {
        padding: 30px !important;
        background: #ffffff !important;
        border: none !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .form-control { width:100%; }
    .select2-container--default .select2-selection--multiple {
        border:1px solid #dbeafe !important;
        border-radius:10px !important;
        padding:6px !important;
        min-height:45px;
        background:#f8fbff !important;
    }
    .btn-main {
        background:#2563eb; color:#fff; border:none; border-radius:10px;
        padding:10px 18px; cursor:pointer; font-weight:600;
        transition: all 0.3s ease;
    }
    .btn-main:hover { opacity:.9; }
    .btn-main:disabled { background: #94a3b8; cursor: not-allowed; }
    
    .ai-response-card {
        border:1px solid #e2e8f0;
        border-radius:14px;
        padding:25px;
        margin-top:10px;
        background:#fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .error-text { color:red; margin-top:8px; font-size: 0.9em; }
    .info-text { color:#64748b; font-size:13px; margin-top:6px; }
</style>
@endpush

@section('content')
<div class="main">
    <div class="dashboard-container">
        <div class="content">
            <div class="card filter-card">
                <form id="aiForm" action="{{ route('ai.generate') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="fw-bold mb-2">Pilih Bahan Makanan</label>
                        <select name="bahan[]" class="form-control bahan-select" multiple required>
                            @foreach($foods as $food)
                                <option value="{{ $food['nama'] }}">{{ $food['nama'] }}</option>
                            @endforeach
                        </select>
                        <small class="info-text">Pilih satu atau lebih bahan yang tersedia.</small>
                        @error('bahan') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <br>
                    <button type="submit" id="submitBtn" class="btn-main">
                        Cari Rekomendasi
                    </button>
                </form>
            </div>

            @if($errors->has('ai'))
                <div class="card" style="margin-top:20px; color:#b91c1c; background: #fef2f2; border:1px solid #fecaca; padding: 15px; border-radius: 10px;">
                    <strong>Error:</strong> {{ $errors->first('ai') }}
                </div>
            @endif

           {{-- Menampilkan Hasil AI --}}
            @if(isset($hasil) && !empty($hasil))
                <div class="card ai-response-card">
                    <h3 style="color: #1e3a8a; margin-bottom: 20px;">Hasil Rekomendasi Menu</h3>
                    <hr>
                    <div id="result-container" style="line-height: 1.8; color: #334155; font-size: 15px;">
                        {!! nl2br(e($hasil)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    $('.bahan-select').select2({ placeholder: "Cari bahan...", width: "100%" });

    // Fungsi untuk merapikan teks dari AI agar tidak banyak simbol **
    let content = $('#ai-formatted-content').html();
    if(content) {
        // Membersihkan simbol ** dan membuatnya lebih rapi
        content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        content = content.replace(/---/g, '<hr style="margin: 20px 0;">');
        $('#ai-formatted-content').html(content);
    }

    $('#aiForm').on('submit', function() {
        $('#submitBtn').html('Sedang meracik menu...').prop('disabled', true);
    });

    // Fungsi ini mencari tanda bintang atau tanda pemisah 
    // dan mengubahnya menjadi elemen list atau baris baru
    let container = $('#result-container');
    let text = container.html();
    
    // Mengubah format "Nama Menu *" menjadi format yang lebih bersih
    // Kita ganti tanda * dengan <br> agar turun ke bawah
    let formatted = text
        .replace(/\*/g, '<br>&nbsp;&nbsp;• ') 
        .replace(/1\./g, '<br><strong>1.</strong>')
        .replace(/2\./g, '<br><strong>2.</strong>')
        .replace(/3\./g, '<br><strong>3.</strong>');
        
    container.html(formatted);
});
</script>
@endpush