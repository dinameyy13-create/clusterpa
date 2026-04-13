@extends('layouts.app')

@section('title', 'Rekomendasi Menu')
@section('page-title', 'Rekomendasi Menu')
@section('breadcrumb', 'Rekomendasi Menu')
@push('styles')
<style>
/* FILTER */
.filter-card {
  margin-bottom: 20px;
}

.filter-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.form-group label {
  font-size: 13px;
  color: #6b7280;
  display: block;
  margin-bottom: 6px;
}

.form-group select {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #dbeafe;
  background: #f8fbff;
}

/* INFO */
.info-card {
  margin-bottom: 20px;
  background: #eff6ff;
}

/* MENU */
.menu-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}

.menu-card {
  background: white;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}

.menu-title {
  font-weight: 700;
  margin-bottom: 10px;
  color: #1e3a8a;
}

.menu-card ul {
  padding-left: 18px;
}

.menu-card li {
  margin-bottom: 6px;
}
</style>
@endpush

@section('content')

<!-- ═══════════════ SIDEBAR (sama seperti dashboard) ═══════════════ -->

<!-- ═══════════════ MAIN ═══════════════ -->
<div class="main">
  <div class="dashboard-container">

    <div class="content">

      <!-- FILTER -->
      <div class="card filter-card">
        <div class="filter-grid">

          <div class="form-group">
            <label>Kategori Pengguna</label>
            <select id="kategori">
              <option value="anak">Anak-anak</option>
              <option value="remaja">Remaja</option>
              <option value="ibu">Ibu Hamil / Lansia</option>
            </select>
          </div>

          <div class="form-group">
            <label>Tujuan</label>
            <select id="tujuan">
              <option value="mbg">Menu Bergizi (MBG)</option>
              <option value="stunting">Pencegahan Stunting</option>
            </select>
          </div>

          <div class="form-group" style="display:flex;align-items:end;">
            <button class="btn-main" onclick="getRekomendasi()">
              🍽️ Tampilkan Rekomendasi
            </button>
          </div>

        </div>
      </div>

      <!-- INFO -->
      <div class="card info-card" id="infoBox" style="display:none;">
        <div class="card-title">Informasi Kebutuhan Gizi</div>
        <div id="infoText"></div>
      </div>

      <!-- HASIL -->
      <div class="menu-grid" id="menuBox" style="display:none;">

        <!-- SARAPAN -->
        <div class="card menu-card">
          <div class="menu-title">🌅 Sarapan</div>
          <ul id="sarapan"></ul>
        </div>

        <!-- SIANG -->
        <div class="card menu-card">
          <div class="menu-title">🍛 Makan Siang</div>
          <ul id="siang"></ul>
        </div>

        <!-- MALAM -->
        <div class="card menu-card">
          <div class="menu-title">🌙 Makan Malam</div>
          <ul id="malam"></ul>
        </div>

      </div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
async function getRekomendasi() {

  document.getElementById("infoText").innerHTML = "Loading...";

  try {
    let res = await fetch("http://127.0.0.1:5000/cluster");
    let data = await res.json();

    console.log(data); // 🔥 cek data masuk

    // 🔍 pisahkan cluster
    let protein = data.filter(i => i.cluster == 0);
    let karbo = data.filter(i => i.cluster == 1);
    let seimbang = data.filter(i => i.cluster == 3);

    console.log("protein:", protein.length);
    console.log("karbo:", karbo.length);
    console.log("seimbang:", seimbang.length);

    // fungsi ambil random aman
    function ambil(arr) {
      if (arr.length === 0) return null;
      return arr[Math.floor(Math.random() * arr.length)];
    }

    // 🍳 sarapan
    let sarapan = [ambil(karbo), ambil(protein), ambil(seimbang)];

    // 🍱 siang
    let siang = [ambil(karbo), ambil(protein), ambil(seimbang)];

    // 🌙 malam
    let malam = [ambil(seimbang), ambil(protein), ambil(seimbang)];

    // 🔥 fungsi tampilkan (AMAN)
    function tampilkan(list) {
      return list.map(item => {
        if (!item) return `<li>-</li>`;

        return `
          <li>
            <b>${item.Menu || item.name || '-'}</b><br>
            <small>
              Protein: ${item["Protein (g)"] || 0}g |
              Kalori: ${item["Energy (kJ)"] || 0}
            </small>
          </li>
        `;
      }).join("");
    }

    document.getElementById("sarapan").innerHTML = tampilkan(sarapan);
    document.getElementById("siang").innerHTML = tampilkan(siang);
    document.getElementById("malam").innerHTML = tampilkan(malam);

    document.getElementById("menuBox").style.display = "grid";

    document.getElementById("infoBox").style.display = "block";
    document.getElementById("infoText").innerHTML =
      "Menu disusun dari kombinasi Protein, Karbohidrat, dan Gizi Seimbang (K-Means)";

  } catch (error) {
    console.error(error);
    alert("Gagal ambil data API");
  }
}
</script>
@endpush