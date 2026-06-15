@extends('layouts.app')

@section('title', 'Rekomendasi Menu')
@section('page-title', 'Rekomendasi Menu')
@section('breadcrumb', 'Rekomendasi Menu')

@push('styles')
<style>
/* FILTER */
.filter-card{
  margin-bottom:24px;
  padding:22px;
  border-radius:22px;
  background:linear-gradient(
    135deg,
    #eff6ff 0%,
    #ffffff 100%
  );
  border:1px solid #dbeafe;
  box-shadow:
    0 10px 30px rgba(37,99,235,0.08);
}

.filter-grid{
  display:grid;
  grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));
  gap:18px;
  align-items:end;
}

.form-group label{
  display:block;
  margin-bottom:8px;
  font-size:13px;
  font-weight:600;
  color:#1e3a8a;
}

.form-group select{
  width:100%;
  padding:14px 16px;
  border-radius:16px;
  border:1px solid #bfdbfe;
  background:#ffffff;
  font-size:14px;
  color:#1e293b;
  transition:all .25s ease;
  outline:none;
  appearance:none;

  background-image:
    linear-gradient(45deg, transparent 50%, #2563eb 50%),
    linear-gradient(135deg, #2563eb 50%, transparent 50%);

  background-position:
    calc(100% - 18px) calc(50% - 3px),
    calc(100% - 12px) calc(50% - 3px);

  background-size:6px 6px;
  background-repeat:no-repeat;
}

.form-group select:hover{
  border-color:#60a5fa;
}

.form-group select:focus{
  border-color:#2563eb;
  box-shadow:
    0 0 0 4px rgba(37,99,235,0.12);
}

.btn-main{
  width:100%;
  border:none;
  padding:14px 18px;
  border-radius:16px;
  background:linear-gradient(
    135deg,
    #2563eb,
    #1d4ed8
  );
  color:white;
  font-weight:700;
  font-size:14px;
  cursor:pointer;
  transition:all .25s ease;
  box-shadow:
    0 8px 18px rgba(37,99,235,.25);
}

.btn-main:hover{
  transform:translateY(-2px);
  box-shadow:
    0 12px 24px rgba(37,99,235,.35);
}

/* INFO */
.info-card {
  margin-bottom: 20px;
  background: #eff6ff;
}

/* MENU GRID */
.menu-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}

/* CARD */
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

/* LIST STYLE */
.menu-card ul {
  list-style: none;
  padding-left: 0;
}

.menu-card li {
  margin-bottom: 8px;
  font-size: 14px;
}

.menu-image{
  width:100%;
  height:220px;
  object-fit:cover;
  border-radius:14px;
  margin-bottom:14px;
  display:block;
}

.ompreng-wrapper{
  position:relative;
  width:320px;
  margin:auto;
}

.ompreng-bg{
  width:100%;
  display:block;
}

.ompreng-item{
  position:absolute;
  object-fit:contain;
  width:auto;
  height:auto;
  max-width:100%;
  max-height:100%;
}

.nasi{
  height:99px;
  left:28px;
  bottom:58px;
}

.lauk{
  height:90px;
  right:80px;
  bottom:60px;
}

.nabati{
  height:88px;
  right:15px;
  bottom:60px;
}

.sayur{
  height:88px;
  top:70px;
  left:115px;
}

.buah{
  height:75px;
  top:70px;
  right:20px;
}

.susu{
  height:84px;
  top:70px;
  left:18px;
}

.badge-ai {
  display: inline-block;
  background: #dbeafe;
  color: #1e40af;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  margin-top: 10px;
}

.score-box {
  background: #f8fafc;
  padding: 10px;
  border-radius: 10px;
  margin-top: 12px;
  font-size: 13px;
  border: 1px solid #e2e8f0;
}

</style>
@endpush

@section('content')

<div class="main">
  <div class="dashboard-container">
    <div class="content">
      <!-- FILTER -->
      <div class="card filter-card">

        <div style="
          display:flex;
          align-items:center;
          gap:10px;
          margin-bottom:20px;
        ">
        </div>

        <div class="filter-grid">

          <div class="form-group">
            <label>Kategori Pengguna</label>

            <select id="kategori">

              <option value="paud">
                PAUD / TK / RA
              </option>

              <option value="sd">
                SD / MI
              </option>

              <option value="smp">
                SMP / MTS
              </option>

              <option value="sma">
                SMA / MA
              </option>

              <option value="ibu">
                Ibu Hamil / Menyusui
              </option>

            </select>
          </div>

          <div class="form-group" style="
            display:flex;
            align-items:end;
          ">
            <button
              class="btn-main"
              onclick="getRekomendasi()"
            >
              ✨ Generate Menu MBG
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
      <div class="menu-grid" id="menuBox" style="display:none;"></div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>

async function getRekomendasi() {

    let kategori =
        document.getElementById("kategori").value;

    document.getElementById("infoText").innerHTML =
        "Loading...";

    try {

        let res = await fetch(
            `/rekomendasi/data?kategori=${kategori}`
        );

        let result = await res.json();

        console.log(result);

        if (!result || !result.data) {

            alert("Data tidak ditemukan");
            return;
        }

        let data = result.data;

        let html = data.map(item => {

            let menu =
                item?.hasil?.menu || {};

            let total =
                item?.hasil?.total || {};

            let target =
                item?.target || {};

            let rekomendasi =
                item?.rekomendasi || {};

            return `

            <div class="card menu-card">

                <div class="ompreng-wrapper">

                    <img
                        src="/images/Makanan/Omprengmbg.png"
                        class="ompreng-bg"
                    >

                    ${menu?.pokok?.gambar ? `
                    <img
                        src="${menu.pokok.gambar}"
                        class="ompreng-item nasi"
                    >
                    ` : ''}

                    ${menu?.hewani?.gambar ? `
                    <img
                        src="${menu.hewani.gambar}"
                        class="ompreng-item lauk"
                    >
                    ` : ''}

                    ${menu?.nabati?.gambar ? `
                    <img
                        src="${menu.nabati.gambar}"
                        class="ompreng-item nabati"
                    >
                    ` : ''}

                    ${menu?.sayur?.gambar ? `
                    <img
                        src="${menu.sayur.gambar}"
                        class="ompreng-item sayur"
                    >
                    ` : ''}

                    ${menu?.buah?.gambar ? `
                    <img
                        src="${menu.buah.gambar}"
                        class="ompreng-item buah"
                    >
                    ` : ''}

                    ${menu?.susu?.gambar ? `
                    <img
                        src="${menu.susu.gambar}"
                        class="ompreng-item susu"
                    >
                    ` : ''}

                </div>

                <div class="menu-title">
                    🍱 ${item.hari}
                </div>

                <ul style="
                    margin-top:14px;
                    padding-left:18px;
                    line-height:1.9;
                    font-size:14px;
                ">

                    <li>
                      🍚 ${menu?.pokok?.nama || '-'}
                    </li>

                    <li>
                      🍗 ${menu?.hewani?.nama || '-'}
                    </li>

                    <li>
                      🥜 ${menu?.nabati?.nama || '-'}
                    </li>

                    <li>
                      🥬 ${menu?.sayur?.nama || '-'}
                    </li>

                    <li>
                      🍎 ${menu?.buah?.nama || '-'}
                    </li>

                    ${menu?.susu ? `
                    <li>
                      🥛 ${menu?.susu?.nama}
                    </li>
                    ` : ''}

                </ul>

                <div class="score-box">

                    <b>
                      ${rekomendasi?.label || 'Baik'}
                    </b>

                    <br><br>

                    <small>
                      Score:
                      <b>${item?.hasil?.score || 0}</b>
                    </small>

                </div>

                <div style="
                    font-size:13px;
                    margin-top:12px;
                ">

                    <b>Total Nutrisi:</b>

                    <table style="
                        width:100%;
                        margin-top:8px;
                        font-size:12px
                    ">

                        <tr>
                            <td>Kalori</td>
                            <td>${total?.kalori || 0}</td>
                            <td>/ ${target?.kalori || 0}</td>
                        </tr>

                        <tr>
                            <td>Protein</td>
                            <td>${total?.protein || 0}</td>
                            <td>/ ${target?.protein || 0}</td>
                        </tr>

                        <tr>
                            <td>Karbohidrat</td>
                            <td>${total?.karbohidrat || 0}</td>
                            <td>/ ${target?.karbohidrat || 0}</td>
                        </tr>

                        <tr>
                            <td>Lemak</td>
                            <td>${total?.lemak || 0}</td>
                            <td>/ ${target?.lemak || 0}</td>
                        </tr>

                        <tr>
                            <td>Serat</td>
                            <td>${total?.serat || 0}</td>
                            <td>/ ${target?.serat || 0}</td>
                        </tr>

                    </table>

                </div>

            </div>

            `;

        }).join("");

        document.getElementById("menuBox").innerHTML =
            html;

        document.getElementById("menuBox").style.display =
            "grid";

        document.getElementById("infoBox").style.display =
            "block";

        document.getElementById("infoText").innerHTML =
            "Rekomendasi menu MBG berdasarkan kebutuhan gizi pengguna.";

    } catch (error) {

        console.error(error);

        alert("Gagal mengambil data");
    }
}

</script>
@endpush