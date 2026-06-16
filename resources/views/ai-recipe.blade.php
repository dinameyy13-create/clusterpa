@extends('layouts.app')

@section('title', 'Rekomendasi AI')
@section('page-title', 'Rekomendasi AI')
@section('breadcrumb', 'Rekomendasi AI')

@push('styles')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<style>
.topbar{
    height: var(--header-h);
    background: white;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    padding: 0 20px;
    gap: 12px;
}
.filter-card{
    padding:28px;
    border-radius:16px;
}

.form-group label{
    display:block;
    margin-bottom:12px;
    font-size:18px;
    font-weight:700;
    color:#1e293b;
}

.info-text{
    color:#64748b;
    font-size:13px;
    margin-top:8px;
}

.error-text{
    color:#dc2626;
    margin-top:8px;
}
.select2-container--default .select2-selection--multiple{
    min-height:52px;
    border-radius:12px !important;
    border:1px solid #cbd5e1 !important;
    background:#f8fbff !important;
    padding:8px;
}
.select2-selection__choice{
    background:#2563eb !important;
    color:white !important;
    border:none !important;
    border-radius:8px !important;
    padding:3px 8px;
}
.btn-main{
    background:#2563eb;
    color:white;
    border:none;
    padding:12px 24px;
    border-radius:12px;
    font-weight:600;
    transition:.3s;
}
.btn-main:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(37,99,235,.25);
}
.btn-main:disabled{
    opacity:.7;
}
.ai-response-card{
    margin-top:24px;
    border-radius:16px;
    padding:28px;
    background:#ffffff;
    border:1px solid #e2e8f0;
    box-shadow:0 10px 20px rgba(0,0,0,.06);
}
.ai-title{
    font-size:28px;
    font-weight:700;
    color:#1d4ed8;
    margin-bottom:18px;
}
#result-container{
    background:#f8fafc;
    border-radius:12px;
    padding:22px;
    line-height:2;
    font-size:15px;
    border:1px solid #e2e8f0;
    white-space: pre-wrap;
    line-height: 1.6;
}
#loadingAI{
    display:none;
    margin-top:15px;
    color:#2563eb;
    font-weight:600;
}

/* =======================================================
   RESPONSIVE GLOBAL
======================================================= */

html,
body{
    overflow-x:hidden;
}

/* ---------- Tablet ---------- */

@media (max-width:1024px){

    .grid-2,
    .grid-3{
        grid-template-columns:1fr;
    }

    .page-content{
        padding:20px;
    }

}


/* ---------- Mobile ---------- */

@media (max-width:768px){

    /* Sidebar */

    .sidebar{
        transform:translateX(-100%);
        z-index:999;
    }

    .sidebar.open{
        transform:translateX(0);
    }

    /* Main */

    .main-wrapper{
        margin-left:0;
        width:100%;
    }

    /* Topbar */

    .topbar{
        padding:0 14px;
        gap:10px;
    }

    .sidebar-toggle{
        display:flex;
        width:36px;
        height:36px;
        flex-shrink:0;
        align-items:center;
        justify-content:center;
    }

    .topbar-title{
        flex:1;
    }

    .topbar-title h1{
        font-size:18px;
        line-height:1.2;
    }

    .breadcrumb{
        font-size:11px;
        margin-top:2px;
    }

    .topbar-info{
        display:none;
    }

    /* Content */

    .page-content{
        padding:14px;
    }

    /* Card */

    .card-header{
        flex-direction:column;
        align-items:flex-start;
        gap:8px;
    }

    .card-body{
        padding:16px;
    }

    /* Grid */

    .stats-grid{
        grid-template-columns:1fr;
    }

}


/* ---------- HP kecil ---------- */

@media (max-width:480px){

    .page-content{
        padding:10px;
    }

    .card-header{
        padding:14px;
    }

    .card-body{
        padding:14px;
    }

    .section-title{
        font-size:20px;
    }

    .section-desc{
        font-size:12px;
    }

    .topbar-title h1{
        font-size:17px;
    }

}
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
                    <button
                        type="submit"
                        id="submitBtn"
                        class="btn-main"
                    >
                        Cari Rekomendasi
                    </button>

                    <div id="loadingAI">

                        AI sedang mencari menu terbaik...

                    </div>
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

                    <div class="ai-title">

                        Hasil Rekomendasi AI

                    </div>

                    <div id="result-container">

                        {{$hasil}}

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

const sidebar = document.getElementById('sidebar');
const toggle = document.getElementById('sidebarToggle');

toggle?.addEventListener('click', () => {
    sidebar.classList.toggle('open');
});

document.addEventListener('click', function (e) {

    if (
        window.innerWidth <= 768 &&
        !sidebar.contains(e.target) &&
        !toggle.contains(e.target)
    ) {
        sidebar.classList.remove('open');
    }

});

$(function(){

    $('.bahan-select').select2({

        placeholder:"Cari bahan makanan...",

        width:"100%"

    });

    $('#aiForm').on('submit',function(){

        $('#submitBtn')

            .prop('disabled',true)

            .text('Memproses...');

        $('#loadingAI').show();

    });

    let container=$("#result-container");

    if(container.length){

        let html=container.html();

        html=html

        .replace(/\*\*(.*?)\*\*/g,"<strong>$1</strong>")

        .replace(/---/g,"<hr>")

        .replace(/\n/g,"<br>");

        container.html(html);

    }

});
</script>
@endpush