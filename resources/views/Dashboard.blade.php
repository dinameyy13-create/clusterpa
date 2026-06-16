@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
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
.mini-stat{
    background:#F9FAFB;
    border-radius:12px;
    padding:12px;
    text-align:center;
}

.mini-stat-value{
    font-size:18px;
    font-weight:700;
    color:#111827;
}

.mini-stat-label{
    font-size:11px;
    color:#6B7280;
    margin-top:4px;
}

.food-list{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.food-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:10px 14px;
    background:var(--surface-3);
    border-radius:8px;
    font-size:13px;
}

.food-row-left{
    display:flex;
    align-items:center;
    gap:10px;
}

.food-cat{
    font-size:11px;
    color:var(--text-muted);
}

/* ============================= */
/* RESPONSIVE DASHBOARD */
/* ============================= */

@media (max-width: 992px){

    .stats-grid{
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .grid-2{
        grid-template-columns: 1fr !important;
    }

}
/* =======================================================
   RESPONSIVE GLOBAL
======================================================= */

html,
body{
    overflow-x:hidden;
}
@media (max-width:1024px){

    .grid-2,
    .grid-3{
        grid-template-columns:1fr;
    }

    .page-content{
        padding:20px;
    }

}
/* ---------- Tablet ---------- */

@media (max-width: 768px){
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

    /* Statistik */
    .stats-grid{
        grid-template-columns:1fr !important;
        gap:16px;
    }

    .stat-card{
        padding:18px !important;
    }

    .stat-value{
        font-size:28px !important;
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

    /* Ringkasan cluster */
    .card-body > div[style*="grid-template-columns:repeat(2,1fr)"]{
        display:grid !important;
        grid-template-columns:1fr !important;
    }

    /* Mini stat */
    .mini-stat{
        padding:10px;
    }

    .mini-stat-value{
        font-size:16px;
    }

    /* Info bawah */
    div[style*="linear-gradient(135deg,#EFF6FF,#DBEAFE)"]{

        flex-direction:column !important;

        align-items:flex-start !important;

        padding:18px !important;

    }

}

@media (max-width:576px){

    .food-row{

        flex-direction:column;

        align-items:flex-start;

        gap:10px;

    }

    .food-row-left{

        width:100%;

    }

    .cluster-badge{

        align-self:flex-start;

    }

    /* Mini statistik dalam cluster */

    .card-body div[style*="grid-template-columns:repeat(3,1fr)"]{

        display:grid !important;

        grid-template-columns:1fr !important;

        gap:10px;

    }

}
</style>
@endpush

@section('content')

<div class="section-header">
    <h2 class="section-title">Dashboard Analitik</h2>

    <p class="section-desc">
        Ringkasan hasil pengelompokan makanan menggunakan algoritma K-Means
    </p>
</div>

<!-- STAT -->
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <circle cx="3" cy="6" r="2"/>
                <circle cx="21" cy="6" r="2"/>
                <circle cx="3" cy="18" r="2"/>
                <circle cx="21" cy="18" r="2"/>
                <path d="M5 6h14M5 18h14"/>
            </svg>
        </div>

        <div class="stat-value">
            {{ $stats['total_cluster'] }}
        </div>

        <div class="stat-label">
            Jumlah Cluster
        </div>

        <div class="stat-change">
            K-Means k=4
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 11l19-9-9 19-2-8-8-2z"/>
            </svg>
        </div>

        <div class="stat-value">
            {{ $stats['total_makanan'] }}
        </div>

        <div class="stat-label">
            Total Data Makanan
        </div>

        <div class="stat-change">
            Tersegmentasi
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
        </div>

        <div class="stat-value">
            {{ $stats['avg_kalori'] }}
        </div>

        <div class="stat-label">
            Rata-rata Kalori (kcal)
        </div>

        <div class="stat-change">
            Per 100g bahan
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 6v6l4 2"/>
            </svg>
        </div>

        <div class="stat-value">
            {{ $stats['avg_protein'] }}g
        </div>

        <div class="stat-label">
            Rata-rata Protein
        </div>

        <div class="stat-change">
            Per 100g bahan
        </div>
    </div>

</div>

<!-- MAIN GRID -->
<div class="grid-2" style="margin-bottom:24px;">

    <!-- RINGKASAN CLUSTER -->
    <div class="card">

        <div class="card-header">
            <div>

                <div class="card-title">
                    Ringkasan Hasil Clustering
                </div>

                <div class="card-subtitle">
                    Ringkasan pola nutrisi dan karakteristik dominan pada setiap cluster hasil K-Means
                </div>

            </div>
        </div>

        <div class="card-body">

            <div style="
                display:grid;
                grid-template-columns:repeat(2,1fr);
                gap:14px;
            ">

                @php

                $contoh = [

                    'Protein Sedang' => [
                        'Telur Dadar',
                        'Tahu Goreng',
                        'Ikan Bandeng'
                    ],

                    'Tinggi Energi Lengkap' => [
                        'Ayam Goreng',
                        'Tempe Goreng',
                        'Rendang'
                    ],

                    'Tinggi Karbohidrat' => [
                        'Nasi Putih',
                        'Bihun Goreng',
                        'Kentang Rebus'
                    ],

                    'Seimbang' => [
                        'Sayur Sop',
                        'Capcay',
                        'Gado-Gado'
                    ]

                ];

                @endphp

                @foreach($clusterInfo as $cluster)

                <div style="
                    border:1px solid #E5E7EB;
                    border-radius:16px;
                    padding:16px;
                    background:#fff;
                ">

                    <!-- TOP -->
                    <div style="
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                        margin-bottom:14px;
                    ">

                        <div style="
                            display:flex;
                            align-items:center;
                            gap:10px;
                        ">

                            <div style="
                                width:12px;
                                height:12px;
                                border-radius:50%;
                                background:{{ $cluster['color'] }};
                            "></div>

                            <div>

                                <div style="
                                    font-size:14px;
                                    font-weight:700;
                                    color:#111827;
                                ">
                                    {{ $cluster['label'] }}
                                </div>

                                <div style="
                                    font-size:12px;
                                    color:#6B7280;
                                ">
                                    Cluster {{ $loop->iteration }}
                                </div>

                            </div>

                        </div>

                        <div style="
                            background:#F3F4F6;
                            padding:6px 10px;
                            border-radius:999px;
                            font-size:12px;
                            font-weight:600;
                            color:#374151;
                        ">
                            {{ $cluster['count'] }} data
                        </div>

                    </div>

                    <!-- STATS -->
                    <div style="
                        display:grid;
                        grid-template-columns:repeat(3,1fr);
                        gap:10px;
                        margin-bottom:14px;
                    ">

                        <div class="mini-stat">
                            <div class="mini-stat-value">
                                {{ $cluster['avg_kalori'] }}
                            </div>

                            <div class="mini-stat-label">
                                Kalori
                            </div>
                        </div>

                        <div class="mini-stat">
                            <div class="mini-stat-value">
                                {{ $cluster['avg_protein'] }}g
                            </div>

                            <div class="mini-stat-label">
                                Protein
                            </div>
                        </div>

                        <div class="mini-stat">
                            <div class="mini-stat-value">
                                {{ $cluster['avg_karbo'] }}g
                            </div>

                            <div class="mini-stat-label">
                                Karbo
                            </div>
                        </div>

                    </div>

                    <!-- DESKRIPSI -->
                    <div style="
                        font-size:12px;
                        line-height:1.7;
                        color:#6B7280;
                        margin-bottom:14px;
                    ">

                        @if($cluster['label'] == 'Protein Sedang')

                            Cluster dengan kandungan protein yang cukup baik dan nutrisi harian yang relatif stabil.

                        @elseif($cluster['label'] == 'Tinggi Energi Lengkap')

                            Memiliki kandungan energi, protein, dan lemak tinggi sehingga cocok untuk kebutuhan gizi tinggi.

                        @elseif($cluster['label'] == 'Tinggi Karbohidrat')

                            Didominasi makanan sumber karbohidrat sebagai penyedia energi utama dalam menu harian.

                        @elseif($cluster['label'] == 'Seimbang')

                            Memiliki komposisi nutrisi yang lebih merata dan cocok untuk variasi menu bergizi.

                        @endif

                    </div>

                    <!-- TAG -->
                    <div style="
                        display:flex;
                        flex-wrap:wrap;
                        gap:8px;
                    ">

                        @foreach($contoh[$cluster['label']] ?? [] as $item)

                        <span style="
                            background:#EFF6FF;
                            color:#1D4ED8;
                            font-size:11px;
                            padding:6px 10px;
                            border-radius:999px;
                        ">
                            {{ $item }}
                        </span>

                        @endforeach

                    </div>

                </div>

                @endforeach

            </div>

        </div>

    </div>

    <!-- DATA MAKANAN -->
    <div class="card">

        <div class="card-header">

            <div>
                <div class="card-title">
                    Data Makanan Terbaru
                </div>

                <div class="card-subtitle">
                    Sampel hasil clustering makanan
                </div>
            </div>

            <a href="{{ route('clustering') }}"
               style="
                    font-size:12px;
                    color:var(--primary);
                    text-decoration:none;
                    font-weight:600;
               ">
                Lihat Semua →
            </a>

        </div>

        <div class="card-body">

            <div class="food-list">

                @php
                    $sample = collect($foods)->shuffle()->take(10);

                    $clusterColors = [
                        '#2563EB',
                        '#F59E0B',
                        '#10B981',
                        '#8B5CF6'
                    ];
                @endphp

                @foreach($sample as $food)

                <div class="food-row">

                    <div class="food-row-left">

                        <span class="cluster-dot"
                              style="
                                background:{{ $clusterColors[$food['cluster']] ?? '#999' }};
                                width:8px;
                                height:8px;
                                border-radius:50%;
                                display:inline-block;
                                flex-shrink:0;
                              ">
                        </span>

                        <div>

                            <div style="
                                font-weight:600;
                                font-size:13px;
                            ">
                                {{ $food['nama'] }}
                            </div>

                            <div class="food-cat">
                                {{ $food['kategori'] }} · {{ $food['kalori'] }} kcal
                            </div>

                        </div>

                    </div>

                    <span class="cluster-badge"
                          style="
                            background:{{ $clusterColors[$food['cluster']] ?? '#999' }}18;
                            color:{{ $clusterColors[$food['cluster']] ?? '#999' }};
                            font-size:11px;
                            padding:3px 8px;
                            border-radius:20px;
                            font-weight:600;
                          ">
                        C{{ $food['cluster'] + 1 }}
                    </span>

                </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

<!-- INFO -->
<div style="
    background:linear-gradient(135deg,#EFF6FF,#DBEAFE);
    border:1px solid #BFDBFE;
    border-radius:16px;
    padding:20px 24px;
    display:flex;
    align-items:center;
    gap:16px;
">

    <div style="font-size:32px;">
        📌
    </div>

    <div>

        <div style="
            font-size:15px;
            font-weight:700;
            color:#1E40AF;
            font-family:'Space Grotesk',sans-serif;
        ">
            Tentang Algoritma K-Means
        </div>

        <div style="
            font-size:13px;
            color:#3B82F6;
            margin-top:4px;
            line-height:1.6;
        ">

            Sistem menggunakan algoritma K-Means dengan
            <strong>k=4 cluster</strong>
            dan
            <strong>8 parameter nutrisi</strong>
            (kalori, protein, karbohidrat, lemak, serat, zat besi, kalsium, vitamin C).

            Data dinormalisasi sebelum proses clustering untuk menghasilkan pengelompokan makanan berdasarkan kemiripan karakteristik nutrisi.

        </div>

    </div>

</div>

@endsection

@push('scripts')
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
</script>
@endpush