@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
.dist-bar-wrap { display: flex; flex-direction: column; gap: 12px; }
.dist-bar-row { display: flex; align-items: center; gap: 12px; }
.dist-bar-label { width: 160px; font-size: 12px; font-weight: 600; color: var(--text-secondary); flex-shrink: 0; }
.dist-bar-track { flex: 1; height: 10px; background: var(--surface-3); border-radius: 5px; overflow: hidden; }
.dist-bar-fill { height: 100%; border-radius: 5px; transition: width 1s ease; }
.dist-bar-count { width: 30px; text-align: right; font-size: 13px; font-weight: 700; color: var(--text-primary); }

.food-list { display: flex; flex-direction: column; gap: 8px; }
.food-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px;
    background: var(--surface-3);
    border-radius: 8px;
    font-size: 13px;
}
.food-row-left { display: flex; align-items: center; gap: 10px; }
.food-cat { font-size: 11px; color: var(--text-muted); }
</style>
@endpush

@section('content')
<div class="section-header">
    <h2 class="section-title">Dashboard Analitik</h2>
    <p class="section-desc">Ringkasan hasil pengelompokan makanan menggunakan algoritma K-Means</p>
</div>

<!-- Stat Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><circle cx="3" cy="6" r="2"/><circle cx="21" cy="6" r="2"/><circle cx="3" cy="18" r="2"/><circle cx="21" cy="18" r="2"/><path d="M5 6h14M5 18h14"/></svg>
        </div>
        <div class="stat-value">{{ $stats['total_cluster'] }}</div>
        <div class="stat-label">Jumlah Cluster</div>
        <div class="stat-change">K-Means k=4</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['total_makanan'] }}</div>
        <div class="stat-label">Total Data Makanan</div>
        <div class="stat-change">Tersegmentasi</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-value">{{ $stats['avg_kalori'] }}</div>
        <div class="stat-label">Rata-rata Kalori (kcal)</div>
        <div class="stat-change">Per 100g bahan</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 6v6l4 2"/></svg>
        </div>
        <div class="stat-value">{{ $stats['avg_protein'] }}g</div>
        <div class="stat-label">Rata-rata Protein</div>
        <div class="stat-change">Per 100g bahan</div>
    </div>
</div>

<!-- Main Grid -->
<div class="grid-2" style="margin-bottom:24px;">
    <!-- Distribusi Cluster -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Distribusi Cluster</div>
                <div class="card-subtitle">Jumlah data makanan hasil clustering K-Means berdasarkan kemiripan nutrisi</div>
            </div>
        </div>
        <div class="card-body">
            <div class="dist-bar-wrap">
                @php
                    $clusterIcons = ['🔵','🟡','🟢','🟣'];
                    $total = $stats['total_makanan'];
                @endphp
                @foreach($clusterDistribution as $i => $cluster)
                <div class="dist-bar-row">
                    <div class="dist-bar-label">{{ $clusterIcons[$i] }} {{ $cluster['label'] }}</div>
                    <div class="dist-bar-track">
                      @php
                      $percent = $total > 0 ? ($cluster['count']/$total)*100 : 0;
                      @endphp
                      <div class="dist-bar-fill" 
                          style="width: {{ $percent }}%; background: {{ $cluster['color'] }};">
                      </div>
                    </div>
                    <div class="dist-bar-count">{{ $cluster['count'] }}</div>
                </div>
                @endforeach
            </div>

            <!-- Donut Chart -->
            <div style="margin-top:24px;">
                <canvas id="donutChart" width="220" height="220" style="display:block;margin:0 auto;max-width:220px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Foods -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Data Makanan Terbaru</div>
                <div class="card-subtitle">Sampel hasil clustering</div>
            </div>
            <a href="{{ route('clustering') }}" style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">Lihat Semua →</a>
        </div>
        <div class="card-body">
            <div class="food-list">
                @php
                    $sample = array_slice($foods, 0, 8);
                    $clusterColors = ['#2563EB','#F59E0B','#10B981','#8B5CF6'];
                @endphp
                @foreach($sample as $food)
                <div class="food-row">
                    <div class="food-row-left">
                        <span class="cluster-dot" style="background:{{ $clusterColors[$food['cluster']] ?? '#999' }};width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                        <div>
                            <div style="font-weight:600;font-size:13px;">{{ $food['nama'] }}</div>
                            <div class="food-cat">{{ $food['kategori'] }} · {{ $food['kalori'] }} kcal</div>
                        </div>
                    </div>
                    <span class="cluster-badge" style="background:{{ $clusterColors[$food['cluster']] ?? '#999' }}18;color:{{ $clusterColors[$food['cluster']] ?? '#999' }};font-size:11px;padding:3px 8px;border-radius:20px;font-weight:600;">
                        C{{ $food['cluster'] + 1 }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Nutrisi Average per Cluster -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <div>
            <div class="card-title">Perbandingan Rata-rata Nutrisi per Cluster</div>
            <div class="card-subtitle">Nilai rata-rata kandungan nutrisi utama per kelompok</div>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-wrap">
            <canvas id="barAvgChart"></canvas>
        </div>
    </div>
</div>

<!-- Info Banner -->
<div style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:1px solid #BFDBFE;border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px;">
    <div style="font-size:32px;">📌</div>
    <div>
        <div style="font-size:15px;font-weight:700;color:#1E40AF;font-family:'Space Grotesk',sans-serif;">Tentang Algoritma K-Means</div>
        <div style="font-size:13px;color:#3B82F6;margin-top:4px;line-height:1.6;">
            Sistem menggunakan algoritma K-Means dengan <strong>k=4 cluster</strong> dan <strong>8 parameter nutrisi</strong> 
            (kalori, protein, karbohidrat, lemak, serat, zat besi, kalsium, vitamin C). 
            Data dinormalisasi sebelum proses clustering untuk hasil yang optimal.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const clusterData = @json($clusterDistribution);
const foods = @json($foods);

const clusterColors = ['#2563EB','#F59E0B','#10B981','#8B5CF6'];

const clusterLabels = [
    'Seimbang',
    'Tinggi Karbohidrat',
    'Rendah Nutrisi',
    'Tinggi Energi & Protein'
];

// HITUNG DISTRIBUSI DARI FOODS
const clusterCount = {};

foods.forEach(f => {
    clusterCount[f.cluster] = (clusterCount[f.cluster] || 0) + 1;
});

const sortedClusters = Object.keys(clusterCount)
    .map(c => parseInt(c))
    .sort((a,b) => a - b);

// Donut Chart
const donutCtx = document.getElementById('donutChart').getContext('2d');
new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: sortedClusters.map(c => 
            `Cluster ${c + 1} - ${clusterLabels[c]}`
        ),
        datasets: [{
            data: sortedClusters.map(c => clusterCount[c]),
            backgroundColor: sortedClusters.map(c => clusterColors[c]),
            borderWidth: 3,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '70%',
        plugins: {
            legend: {
                display: false // 🔥 ini yang penting
            }
        }
    }
});

// Bar Avg Chart
const avgData = {};

for (let c = 0; c < 4; c++) {
    const cf = foods.filter(f => f.cluster === c);
    if (cf.length === 0) continue;
    avgData[c] = {
        protein: (cf.reduce((s,f)=>s+f.protein,0)/cf.length).toFixed(1),
        karbohidrat: (cf.reduce((s,f)=>s+f.karbohidrat,0)/cf.length).toFixed(1),
        lemak: (cf.reduce((s,f)=>s+f.lemak,0)/cf.length).toFixed(1),
        serat: (cf.reduce((s,f)=>s+f.serat,0)/cf.length).toFixed(1),
    };
}

const barCtx = document.getElementById('barAvgChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: ['Protein (g)', 'Karbohidrat (g)', 'Lemak (g)', 'Serat (g)'],
        datasets: Object.keys(avgData).map(c => ({
            label: `Cluster ${parseInt(c)+1} - ${clusterLabels[c]}`,
            data: [avgData[c].protein, avgData[c].karbohidrat, avgData[c].lemak, avgData[c].serat],
            backgroundColor: clusterColors[c] + 'CC',
            borderColor: clusterColors[c],
            borderWidth: 1.5,
            borderRadius: 6,
        }))
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { family: 'Plus Jakarta Sans', size: 12 }, boxWidth: 12, padding: 16 }},
            tooltip: { bodyFont: { family: 'Plus Jakarta Sans' }, titleFont: { family: 'Space Grotesk' } }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#EFF6FF' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 12 } } }
        }
    }
});
</script>
@endpush