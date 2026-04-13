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

.dropdown-filter {
    position: relative;
}

#dropdownBtn {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 110%;
    left: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    min-width: 220px;
    z-index: 100;
}

.dropdown-menu label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    cursor: pointer;
}

#pagination button {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
}

#pagination button:hover {
    background: #f1f5f9;
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
            <div class="dropdown-filter">
            <button id="dropdownBtn">Pilih Cluster ▾</button>

            <div id="dropdownMenu" class="dropdown-menu">
                <label><input type="checkbox" value="0"> Cluster 1 - Seimbang</label>
                <label><input type="checkbox" value="1"> Cluster 2 - Tinggi Karbohidrat</label>
                <label><input type="checkbox" value="2"> Cluster 3 - Rendah Nutrisi</label>
                <label><input type="checkbox" value="3"> Cluster 4 - Tinggi Energi & Protein</label>
            </div>
        </div>
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
        <div id="pagination" style="margin-top:16px;display:flex;gap:6px;flex-wrap:wrap;"></div>
    </div>
</div>

<!-- Cluster Legend -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:20px;">
    @php
        $legends = [
            ['color'=>'#2563EB','label'=>'Cluster 1 – Seimbang','desc'=>'Daging, ikan, telur, kacang-kacangan'],
            ['color'=>'#F59E0B','label'=>'Cluster 2 – Tinggi Karbohidrat','desc'=>'Nasi, roti, kentang, oatmeal'],
            ['color'=>'#10B981','label'=>'Cluster 3 – Rendah Nutrisi','desc'=>'Menu olahan dengan profil nutrisi seimbang'],
            ['color'=>'#8B5CF6','label'=>'Cluster 4 – Tinggi Energi & Serat','desc'=>'Sayuran dan buah-buahan segar'],
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
let currentPage = 1;
const rowsPerPage = 10; // bisa ubah (10, 20, dll)
const allFoods = @json($foods);
let filteredData = [...allFoods]; 

const clusterColors = ['#2563EB','#F59E0B','#10B981','#8B5CF6'];
const clusterLabels = ['Tinggi Protein','Tinggi Karbohidrat','Seimbang & Bergizi','Tinggi Serat & Vitamin'];
const selectedClusters = Array.from(
    document.querySelectorAll('#clusterFilter input:checked')
).map(cb => cb.value);
const dropdownBtn = document.getElementById('dropdownBtn');
const dropdownMenu = document.getElementById('dropdownMenu');

function renderPagination(totalData) {
    const totalPages = Math.ceil(totalData / rowsPerPage);
    const container = document.getElementById('pagination');

    container.innerHTML = '';

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    // Prev
    container.innerHTML += `
        <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
            Prev
        </button>
    `;

    // First page
    if (startPage > 1) {
        container.innerHTML += `<button onclick="changePage(1)">1</button>`;
        if (startPage > 2) container.innerHTML += `<span>...</span>`;
    }

    // Page numbers (limited)
    for (let i = startPage; i <= endPage; i++) {
        container.innerHTML += `
            <button onclick="changePage(${i})"
                style="${i === currentPage ? 'background:#2563EB;color:white;' : ''}">
                ${i}
            </button>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) container.innerHTML += `<span>...</span>`;
        container.innerHTML += `<button onclick="changePage(${totalPages})">${totalPages}</button>`;
    }

    // Next
    container.innerHTML += `
        <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
            Next
        </button>
    `;
}

function changePage(page) {
    currentPage = page;
    renderTable(filteredData);
}

function renderTable(data) {
    const tbody = document.getElementById('tableBody');
    document.getElementById('resultCount').textContent = `Menampilkan ${data.length} data`;

    // pagination logic
    const start = (currentPage - 1) * rowsPerPage;
    const paginatedData = data.slice(start, start + rowsPerPage);

    if (paginatedData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="12"><div class="empty-state"> Tidak ada data</div></td></tr>';
        return;
    }

    tbody.innerHTML = paginatedData.map((f, i) => {
        const c = f.cluster_display;
        const color = clusterColors[f.cluster] || '#999';

        return `
        <tr>
            <td style="color:var(--text-muted);font-size:12px;">${start + i + 1}</td>
            <td style="font-weight:600;">${f.nama}</td>
            <td>
                <span class="cluster-badge" style="background:${color}18;color:${color};">
                    <span class="cluster-dot" style="background:${color};"></span>
                    ${f.kategori}
                </span>
            </td>
            <td>
                <span class="cluster-badge" style="background:${color}18;color:${color};">
                    <span style="background:${color};"></span>${c}
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

    renderPagination(data.length);
}

function applyFilter() {
    const search = document.getElementById('searchInput').value.toLowerCase();

    const selectedClusters = Array.from(
        document.querySelectorAll('#dropdownMenu input:checked')
    ).map(cb => cb.value);

    filteredData = allFoods.filter(f => {
        const matchSearch = f.nama.toLowerCase().includes(search);

        const matchCluster =
            selectedClusters.length === 0 ||
            selectedClusters.includes(String(f.cluster));

        return matchSearch && matchCluster;
    });

    currentPage = 1;
    renderTable(filteredData);
}

function resetFilter() {
    document.getElementById('searchInput').value = '';

    document.querySelectorAll('#dropdownMenu input')
        .forEach(cb => cb.checked = false);

    filteredData = [...allFoods]; // reset data
    currentPage = 1;
    renderTable(filteredData);
}

// EVENT LISTENER SEARCH
document.getElementById('searchInput').addEventListener('input', applyFilter);

// EVENT LISTENER CHECKBOX DROPDOWN
document.querySelectorAll('#dropdownMenu input')
    .forEach(cb => cb.addEventListener('change', applyFilter));

// DROPDOWN TOGGLE
dropdownBtn.addEventListener('click', () => {
    dropdownMenu.style.display =
        dropdownMenu.style.display === 'block' ? 'none' : 'block';
});

// KLIK LUAR → CLOSE DROPDOWN
document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown-filter')) {
        dropdownMenu.style.display = 'none';
    }
});

// INITIAL RENDER
renderTable(filteredData);
</script>
@endpush