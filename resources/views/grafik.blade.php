@extends('layouts.app')

@section('title', 'Grafik Interaktif')
@section('page-title', 'Grafik Interaktif')
@section('breadcrumb', 'Grafik Interaktif')

@push('styles')
<style>
.chart-tabs {
    display: flex; gap: 4px;
    background: var(--surface-3);
    padding: 4px;
    border-radius: 10px;
    margin-bottom: 20px;
    width: fit-content;
}
.chart-tab {
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; border: none; background: transparent;
    color: var(--text-secondary);
    transition: all 0.2s;
    font-family: inherit;
}
.chart-tab.active {
    background: white;
    color: var(--primary);
    box-shadow: 0 2px 8px rgba(37,99,235,0.12);
}
.chart-panel { display: none; }
.chart-panel.active { display: block; }

.chart-legend {
    display: flex; gap: 16px; flex-wrap: wrap;
    margin-bottom: 16px;
}
.legend-item {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600; color: var(--text-secondary);
}
.legend-dot { width: 10px; height: 10px; border-radius: 50%; }

.radar-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.cluster-info-bar {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;
    margin-bottom: 20px;
}
.cib-item {
    padding: 12px 16px;
    border-radius: 10px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s;
}
.cib-item.active { border-color: currentColor; }
.cib-label { font-size: 11px; font-weight: 700; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; }
.cib-value { font-size: 20px; font-weight: 800; font-family: 'Space Grotesk', sans-serif; }
.cib-sub { font-size: 11px; opacity: 0.7; }
</style>
@endpush

@section('content')
<div class="section-header">
    <h2 class="section-title">Grafik Interaktif</h2>
    <p class="section-desc">Visualisasi distribusi nutrisi makanan berdasarkan hasil clustering K-Means</p>
</div>

<!-- Chart Tabs -->
<div class="card">
    <div class="card-body">
        <div class="chart-tabs">
            <button class="chart-tab active" onclick="switchTab('scatter', this)">📍 Scatter Plot</button>
            <button class="chart-tab" onclick="switchTab('bar', this)">📊 Bar Chart</button>
            <button class="chart-tab" onclick="switchTab('radar', this)">🕸️ Radar Chart</button>
        </div>

        <!-- SCATTER PLOT -->
        <div id="panel-scatter" class="chart-panel active">
            <div class="card-title" style="margin-bottom:6px;">Scatter Plot: Protein vs Kalori</div>
            <div class="card-subtitle" style="margin-bottom:16px;">Distribusi makanan berdasarkan kandungan protein dan kalori per kelompok cluster</div>
            <div class="chart-legend" id="scatterLegend"></div>
            <div class="chart-wrap chart-wrap-tall">
                <canvas id="scatterChart"></canvas>
            </div>
        </div>

        <!-- BAR CHART -->
        <div id="panel-bar" class="chart-panel">
            <div class="card-title" style="margin-bottom:6px;">Bar Chart: Rata-rata Nutrisi per Cluster</div>
            <div class="card-subtitle" style="margin-bottom:16px;">Perbandingan rata-rata kandungan nutrisi utama antar cluster</div>
            <div class="cluster-info-bar" id="clusterInfoBar"></div>
            <div class="chart-wrap chart-wrap-tall">
                <canvas id="barNutriChart"></canvas>
            </div>
        </div>

        <!-- RADAR CHART -->
        <div id="panel-radar" class="chart-panel">
            <div class="card-title" style="margin-bottom:6px;">Radar Chart: Profil Nutrisi per Cluster</div>
            <div class="card-subtitle" style="margin-bottom:16px;">Profil nutrisi menyeluruh setiap cluster, menampilkan kekuatan relatif pada 6 dimensi nutrisi</div>
            <div class="radar-grid">
                @for($i = 0; $i < 4; $i++)
                <div class="card" style="border:none;box-shadow:var(--shadow-sm);">
                    <div class="card-body">
                        <canvas id="radar{{ $i }}" height="260"></canvas>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const clusterColors = ['#2563EB','#F59E0B','#10B981','#8B5CF6'];
const clusterLabels = ['Tinggi Protein','Tinggi Karbohidrat','Seimbang & Bergizi','Tinggi Serat & Vitamin'];
const clusterEmojis = ['🥩','🍚','🥗','🥦'];

let scatterChart, barChart;
const radarCharts = {};

function switchTab(name, btn) {
    document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.chart-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + name).classList.add('active');
}

// Fetch data from API
fetch('{{ route("grafik.data") }}')
    .then(r => r.json())
    .then(data => {
        console.log(data);
        initScatter(data.scatter);
        initBar(data.bar);
        initRadar(data.bar);
    });
    
    function initScatter(scatterData) {
        console.log("Scatter jalan", scatterData);

        const datasets = clusterColors.map((color, c) => ({
            label: clusterLabels[c],
            data: scatterData
                .filter(d => d.cluster === c)
                .map(d => ({
                    x: d.x,
                    y: d.y,
                    nama: d.nama
                })),
            backgroundColor: color,
            pointRadius: 5
        }));

        new Chart(document.getElementById('scatterChart'), {
            type: 'scatter',
            data: { datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: { display: true, text: 'Protein' }
                    },
                    y: {
                        title: { display: true, text: 'Kalori' }
                    }
                }
            }
        });
    }

function initBar(barData) {
    // Cluster info bar
    const infoBar = document.getElementById('clusterInfoBar');
    infoBar.innerHTML = barData.map((b, i) => `
        <div class="cib-item" style="background:${clusterColors[i]}18;color:${clusterColors[i]};">
            <div class="cib-label">${clusterEmojis[i]} ${b.cluster}</div>
            <div class="cib-value">${b.kalori}</div>
            <div class="cib-sub">kcal rata-rata</div>
        </div>`).join('');

    const nutrients = ['protein','karbohidrat','lemak','serat','zat_besi','kalsium'];
    const nutrientLabels = ['Protein','Karbohidrat','Lemak','Serat','Zat Besi','Kalsium'];

    barChart = new Chart(document.getElementById('barNutriChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: nutrientLabels,
            datasets: barData.map((b, i) => ({
                label: b.cluster,
                data: nutrients.map(n => b[n]),
                backgroundColor: clusterColors[i] + 'CC',
                borderColor: clusterColors[i],
                borderWidth: 1.5,
                borderRadius: 6,
            }))
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { font: { family: 'Plus Jakarta Sans', size: 12 }, boxWidth: 12, padding: 16 }},
                tooltip: { bodyFont: { family: 'Plus Jakarta Sans' }, titleFont: { family: 'Space Grotesk' }}
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#EFF6FF' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }}},
                x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 12 }}}
            }
        }
    });
}

function initRadar(barData) {
    const labels = ['Protein', 'Karbohidrat', 'Lemak', 'Serat', 'Zat Besi', 'Kalsium'];
    const keys   = ['protein','karbohidrat','lemak','serat','zat_besi','kalsium'];

    barData.forEach((b, i) => {
        const canvas = document.getElementById(`radar${i}`);
        if (!canvas) return;

        // Normalize to 0-100 for radar
        const maxVals = keys.map((k,ki) => Math.max(...barData.map(bd => bd[k] || 0)));
        const vals = keys.map((k,ki) => maxVals[ki] > 0 ? ((b[k]/maxVals[ki])*100).toFixed(1) : 0);

        radarCharts[i] = new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels,
                datasets: [{
                    label: b.cluster,
                    data: vals,
                    fill: true,
                    backgroundColor: clusterColors[i] + '33',
                    borderColor: clusterColors[i],
                    pointBackgroundColor: clusterColors[i],
                    borderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, labels: { font: { family: 'Space Grotesk', size: 11, weight: '700' }, color: clusterColors[i] }},
                    tooltip: { callbacks: { label: ctx => ` ${ctx.raw}%` }, bodyFont: { family: 'Plus Jakarta Sans' }}
                },
                scales: {
                    r: {
                        beginAtZero: true, max: 100,
                        grid: { color: '#DBEAFE' },
                        angleLines: { color: '#DBEAFE' },
                        pointLabels: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#475569' },
                        ticks: { stepSize: 25, font: { size: 9 }, color: '#94A3B8', backdropColor: 'transparent' }
                    }
                }
            }
        });
    });
}
</script>
@endpush