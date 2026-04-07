@extends('layouts.app')

@section('title', 'Tabel Clustering')
@section('page-title', 'Tabel Clustering')
@section('breadcrumb', 'Tabel Clustering')

@push('styles')
<style>
.table-scroll { overflow-x: auto; }
.nutrisi-pill {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    background: var(--surface-3);
    color: var(--text-secondary);
    min-width: 44px;
    text-align: center;
}
.hasil-count {
    font-size: 13px;
    color: var(--text-muted);
    margin-left: auto;
}
#tableBody tr { cursor: default; }
#tableBody tr:hover td { background: #F0F7FF !important; }
.empty-state {
    text-align: center; padding: 48px; color: var(--text-muted);
    font-size: 14px;
}
</style>
@endpush

@section('content')
<div class="section-header">
    <h2 class="section-title">Tabel Clustering Makanan</h2>
    <p class="section-desc">Data lengkap seluruh makanan beserta hasil pengelompokan K-Means dan nilai nutrisinya</p>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Data Makanan & Cluster</div>
            <div class="card-subtitle">{{ count($foods) }} total makanan dalam 4 cluster</div>
        </div>
        <span id="resultCount" class="hasil-count">Menampilkan {{ count($foods) }} data</span>
    </div>
    <div class="card-body" style="padding-top:16px;">
        <!-- Filter Bar -->
        <div class="filter-bar">
            <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Cari nama makanan..." style="flex:1;min-width:200px;max-width:300px;">
            <select class="filter-select" id="clusterFilter">
                <option value="all">Semua Cluster</option>
                <option value="0">🔵 Cluster 1 – Tinggi Protein</option>
                <option value="1">🟡 Cluster 2 – Tinggi Karbohidrat</option>
                <option value="2">🟢 Cluster 3 – Seimbang & Bergizi</option>
                <option value="3">🟣 Cluster 4 – Tinggi Serat & Vitamin</option>
            </select>
            <select class="filter-select" id="kategoriFilter">
                <option value="all">Semua Kategori</option>
                <option value="Hewani">Hewani</option>
                <option value="Nabati">Nabati</option>
                <option value="Karbohidrat">Karbohidrat</option>
                <option value="Sayuran">Sayuran</option>
                <option value="Buah">Buah</option>
                <option value="Olahan">Olahan</option>
            </select>
            <button onclick="resetFilter()" style="padding:9px 16px;border-radius:8px;border:1.5px solid #DBEAFE;background:white;cursor:pointer;font-size:13px;color:#64748B;font-family:inherit;">Reset</button>
        </div>

        <!-- Table -->
        <div class="table-scroll">
            <table class="data-table" id="foodTable">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Nama Makanan</th>
                        <th>Kategori</th>
                        <th>Cluster</th>
                        <th style="text-align:center;">Kalori<br><small style="font-weight:400;text-transform:none;">(kcal)</small></th>
                        <th style="text-align:center;">Protein<br><small style="font-weight:400;text-transform:none;">(g)</small></th>
                        <th style="text-align:center;">Karbohidrat<br><small style="font-weight:400;text-transform:none;">(g)</small></th>
                        <th style="text-align:center;">Lemak<br><small style="font-weight:400;text-transform:none;">(g)</small></th>
                        <th style="text-align:center;">Serat<br><small style="font-weight:400;text-transform:none;">(g)</small></th>
                        <th style="text-align:center;">Zat Besi<br><small style="font-weight:400;text-transform:none;">(mg)</small></th>
                        <th style="text-align:center;">Kalsium<br><small style="font-weight:400;text-transform:none;">(mg)</small></th>
                        <th style="text-align:center;">Vit C<br><small style="font-weight:400;text-transform:none;">(mg)</small></th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- filled by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Cluster Legend -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:20px;">
    @php
        $legends = [
            ['color'=>'#2563EB','label'=>'Cluster 1 – Tinggi Protein','desc'=>'Daging, ikan, telur, kacang-kacangan'],
            ['color'=>'#F59E0B','label'=>'Cluster 2 – Tinggi Karbohidrat','desc'=>'Nasi, roti, kentang, oatmeal'],
            ['color'=>'#10B981','label'=>'Cluster 3 – Seimbang & Bergizi','desc'=>'Menu olahan dengan profil nutrisi seimbang'],
            ['color'=>'#8B5CF6','label'=>'Cluster 4 – Tinggi Serat & Vitamin','desc'=>'Sayuran dan buah-buahan segar'],
        ];
    @endphp
    @foreach($legends as $leg)
    <div style="background:white;border:1px solid #DBEAFE;border-radius:12px;padding:12px 16px;display:flex;align-items:flex-start;gap:10px;box-shadow:0 1px 4px rgba(37,99,235,0.06);">
        <span style="width:10px;height:10px;border-radius:50%;background:{{ $leg['color'] }};flex-shrink:0;margin-top:4px;"></span>
        <div>
            <div style="font-size:13px;font-weight:700;color:#0F172A;">{{ $leg['label'] }}</div>
            <div style="font-size:12px;color:#64748B;margin-top:2px;">{{ $leg['desc'] }}</div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
const allFoods = @json($foods);
const clusterColors = ['#2563EB','#F59E0B','#10B981','#8B5CF6'];
const clusterLabels = ['Tinggi Protein','Tinggi Karbohidrat','Seimbang & Bergizi','Tinggi Serat & Vitamin'];

function renderTable(data) {
    const tbody = document.getElementById('tableBody');
    document.getElementById('resultCount').textContent = `Menampilkan ${data.length} data`;

    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="12"><div class="empty-state">😕 Tidak ada data yang sesuai filter</div></td></tr>';
        return;
    }

    tbody.innerHTML = data.map((f, i) => {
        const c = f.cluster;
        const color = clusterColors[c] || '#999';
        const label = clusterLabels[c] || `Cluster ${c+1}`;
        return `
        <tr>
            <td style="color:var(--text-muted);font-size:12px;">${i+1}</td>
            <td style="font-weight:600;">${f.nama}</td>
            <td><span style="font-size:12px;background:var(--surface-3);padding:2px 8px;border-radius:4px;color:var(--text-secondary);">${f.kategori}</span></td>
            <td>
                <span class="cluster-badge" style="background:${color}18;color:${color};">
                    <span class="cluster-dot" style="background:${color};"></span>${label}
                </span>
            </td>
            <td style="text-align:center;"><span class="nutrisi-pill">${f.kalori}</span></td>
            <td style="text-align:center;"><span class="nutrisi-pill" style="background:#EFF6FF;color:#2563EB;">${f.protein}</span></td>
            <td style="text-align:center;"><span class="nutrisi-pill" style="background:#FFFBEB;color:#D97706;">${f.karbohidrat}</span></td>
            <td style="text-align:center;"><span class="nutrisi-pill">${f.lemak}</span></td>
            <td style="text-align:center;"><span class="nutrisi-pill" style="background:#ECFDF5;color:#059669;">${f.serat}</span></td>
            <td style="text-align:center;"><span class="nutrisi-pill">${f.zat_besi}</span></td>
            <td style="text-align:center;"><span class="nutrisi-pill">${f.kalsium}</span></td>
            <td style="text-align:center;"><span class="nutrisi-pill" style="background:#FFF7ED;color:#EA580C;">${f.vit_c}</span></td>
        </tr>`;
    }).join('');
}

function applyFilter() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const cluster = document.getElementById('clusterFilter').value;
    const kategori = document.getElementById('kategoriFilter').value;

    let filtered = allFoods.filter(f => {
        const matchSearch = f.nama.toLowerCase().includes(search);
        const matchCluster = cluster === 'all' || f.cluster == cluster;
        const matchKategori = kategori === 'all' || f.kategori === kategori;
        return matchSearch && matchCluster && matchKategori;
    });

    renderTable(filtered);
}

function resetFilter() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clusterFilter').value = 'all';
    document.getElementById('kategoriFilter').value = 'all';
    renderTable(allFoods);
}

document.getElementById('searchInput').addEventListener('input', applyFilter);
document.getElementById('clusterFilter').addEventListener('change', applyFilter);
document.getElementById('kategoriFilter').addEventListener('change', applyFilter);

// Initial render
renderTable(allFoods);
</script>
@endpush