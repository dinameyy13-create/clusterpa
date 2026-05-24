@extends('layouts.app')

@section('title', 'Insight MBG & Stunting')
@section('page-title', 'Insight MBG & Stunting')
@section('breadcrumb', 'Insight MBG & Stunting')

@push('styles')
<style>
.mbg-hero {
    background: linear-gradient(135deg, #1E40AF 0%, #2563EB 60%, #60A5FA 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 28px;
    color: white;
    position: relative; overflow: hidden;
}
.mbg-hero::after {
    content: '';
    position: absolute; right: -20px; top: -20px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.mbg-hero h2 { font-size: 22px; font-weight: 800; font-family: 'Space Grotesk', sans-serif; margin-bottom: 8px; }
.mbg-hero p  { font-size: 14px; opacity: 0.88; line-height: 1.6; max-width: 680px; }

.cluster-section { margin-bottom: 32px; }
.cluster-section-header {
    display: flex; align-items: center; gap: 14px;
    padding: 16px 20px;
    border-radius: 12px 12px 0 0;
    border: 1px solid var(--border); border-bottom: none;
}
.cs-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
}
.cluster-section-body {
    border: 1px solid var(--border);
    border-radius: 0 0 12px 12px;
    background: white;
    padding: 20px;
}

.cluster-foods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px;
    margin-top: 12px;
}

.food-chip-detail {
    padding: 8px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--surface-3);
    font-size: 11px;
    text-align: center;
}

.nutrisi-box {
    background: var(--primary-ghost);
    border: 1px solid var(--primary-pale);
    border-radius: 10px;
    padding: 12px;
    margin-top: 12px;
}

.nutrisi-title {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 6px;
}

.nutrisi-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.nutrisi-list span {
    font-size: 11px;
    background: white;
    border: 1px solid var(--border);
    padding: 3px 8px;
    border-radius: 12px;
}
</style>
@endpush

@section('content')

<!-- HERO -->
<div class="mbg-hero">
    <div style="font-size:11px;font-weight:700;letter-spacing:2px;opacity:0.75;text-transform:uppercase;margin-bottom:8px;">
        Program Makan Bergizi Gratis 2025
    </div>
    <h2>Insight Pola Nutrisi Berdasarkan Clustering</h2>
    <p>
        Analisis K-Means mengelompokkan makanan berdasarkan karakteristik nutrisi.
        Setiap cluster merepresentasikan pola konsumsi yang memiliki dampak berbeda terhadap
        pertumbuhan anak dan risiko stunting.
    </p>
</div>

@php
$clusterIcons = [
    1 => '🥩',
    2 => '🍚',
    3 => '🥗',
    4 => '🥦'
];

$clusterBgs = [
    1 => '#EFF6FF',
    2 => '#FFFBEB',
    3 => '#ECFDF5',
    4 => '#F5F3FF'
];

$clusterManfaat = [
    1 => ['Protein tinggi','Pertumbuhan otot','Cegah anemia'],
    2 => ['Energi utama','Aktivitas harian','Fungsi otak'],
    3 => ['Nutrisi seimbang','Lengkap','Ideal untuk anak'],
    4 => ['Vitamin','Serat','Imunitas'],
];
@endphp

@foreach($clusters as $cluster)
<div class="cluster-section">

    <!-- HEADER -->
    <div class="cluster-section-header" style="background:{{ $clusterBgs[$cluster['id']] ?? '#eee' }}">
        <div class="cs-icon">
            {{ $clusterIcons[$cluster['id']] ?? '🍽️' }}
        </div>

        <div style="flex:1;">
            <div style="font-size:16px;font-weight:800;color:{{ $cluster['color'] ?? '#333' }}">
                <!-- 🔥 LABEL DIJAGA SESUAI SERVICE -->
                Cluster {{ $cluster['id'] }} - {{ $cluster['label'] }}
            </div>
            <div style="font-size:12px;color:#666;">
                {{ $cluster['count'] }} makanan
            </div>
        </div>
    </div>

    <!-- BODY -->
    <div class="cluster-section-body">

        <!-- DESKRIPSI -->
        <p style="font-size:13px;color:#555;line-height:1.6;">
            {{ $cluster['description'] }}
        </p>

        <!-- INSIGHT -->
        <div class="nutrisi-box">
            <div class="nutrisi-title">📊 Insight Nutrisi</div>
            <div class="nutrisi-list">
                <span>🔥 {{ $cluster['avg_kalori'] }} kcal</span>
                <span>💪 {{ $cluster['avg_protein'] }}g protein</span>
                <span>⚖️ {{ $cluster['karakteristik'] ?? 'Analisis nutrisi' }}</span>
            </div>
        </div>

        <!-- PERAN -->
        <div class="nutrisi-box">
            <div class="nutrisi-title">🛡️ Peran terhadap Stunting</div>
            <div class="nutrisi-list">
                <span>{{ $cluster['peran_utama'] ?? '-' }}</span>
                <span>{{ $cluster['fungsi_tubuh'] ?? '-' }}</span>
            </div>
        </div>

        <!-- MANFAAT -->
        <div class="nutrisi-box">
            <div class="nutrisi-title">✅ Manfaat</div>
            <div class="nutrisi-list">
                @foreach(($clusterManfaat[$cluster['id']] ?? []) as $m)
                    <span>{{ $m }}</span>
                @endforeach
            </div>
        </div>

        <!-- SAMPLE MAKANAN -->
        <div style="margin-top:12px;">
            <div style="font-size:12px;font-weight:700;margin-bottom:6px;">
                🍽️ Contoh Makanan
            </div>

            <div class="cluster-foods-grid">
                <!-- 🔥 LIMIT + AMAN -->
                @foreach(collect($cluster['foods'])->take(8) as $food)
                    <div class="food-chip-detail">
                        <div>{{ $food['nama'] }}</div>
                        <small>{{ round($food['kalori']) }} kcal</small>
                    </div>
                @endforeach
            </div>

            <!-- 🔥 INFO SISA -->
            @if(count($cluster['foods']) > 8)
                <small style="color:gray;">
                    +{{ count($cluster['foods']) - 8 }} lainnya
                </small>
            @endif
        </div>

    </div>
</div>
@endforeach

@endsection