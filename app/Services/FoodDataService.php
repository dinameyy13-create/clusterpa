<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class FoodDataService
{
    public function getClusteredFoods()
    {
        $response = Http::get('http://127.0.0.1:5000/cluster');

        if ($response->successful()) {
           return array_map(function ($item) {
            $cluster = (int) ($item['cluster'] ?? 0);
            // mapping kategori berdasarkan cluster
            $kategoriMap = [
                0 => 'Seimbang',
                1 => 'Tinggi Karbohidrat',
                2 => 'Rendah Nutrisi',
                3 => 'Tinggi Energi & Protein',
            ];

            $nama = strtolower(trim($item['Menu'] ?? '-'));

            $bahan_keywords = [

                'tepung',
                'minyak',
                'gula',
                'garam',
                'kemiri',
                'mentega',
                'bumbu',
                'beras',
                'mie soun',
                'soun',
                'tepung roti',
                'maizena',
                'kanji',
                'terigu',
                'penyedap',
                'kaldu',
                'santan mentah',
                'kelapa parut',
                'air',
                'cuka',
                'saus',
                'kecap',
                'cabai rawit',
                'bawang',
                'jahe',
                'kunyit',
                'lengkuas',
            ];

            $isBahan = false;

            if (
                str_contains($nama, 'mentah') ||
                str_contains($nama, 'kering')
            ) {
                $isBahan = true;
            }
            foreach ($bahan_keywords as $k) {
                if (str_contains($nama, $k)) {
                    $isBahan = true;
                    break;
                }
            }

            $kategori_data = $isBahan ? 'bahan' : 'makanan';
            // =========================
            // 🔥 RULE KLASIFIKASI FINAL
            // =========================
            $jenis = match (true) {

                // 🍚 POKOK (HARUS DIAWAL)
                str_starts_with($nama, 'nasi') ||
                str_starts_with($nama, 'mie') ||
                str_starts_with($nama, 'mi ') ||
                str_starts_with($nama, 'lontong') ||
                str_starts_with($nama, 'ketupat') => 'pokok',

                // 🥬 SAYUR (prioritas tinggi biar ga ketukar buah)
                str_contains($nama, 'sayur') ||
                str_contains($nama, 'bayam') ||
                str_contains($nama, 'kangkung') ||
                str_contains($nama, 'wortel') ||
                str_contains($nama, 'daun') ||
                str_contains($nama, 'tumis') ||
                str_contains($nama, 'sop') ||
                str_contains($nama, 'bening') => 'sayur',

                // 🍎 BUAH
                str_contains($nama, 'apel') ||
                str_contains($nama, 'pisang') ||
                str_contains($nama, 'jeruk') ||
                str_contains($nama, 'pepaya') ||
                str_contains($nama, 'mangga') ||
                str_contains($nama, 'semangka') => 'buah',

                // 🍗 HEWANI
                str_contains($nama, 'ayam') ||
                str_contains($nama, 'ikan') ||
                str_contains($nama, 'daging') ||
                str_contains($nama, 'telur') ||
                str_contains($nama, 'hati') => 'hewani',

                // 🥜 NABATI
                str_contains($nama, 'tahu') ||
                str_contains($nama, 'tempe') ||
                str_contains($nama, 'kacang') => 'nabati',

                str_contains($nama, 'susu') ||
                str_contains($nama, 'ultramilk') => 'susu',
                default => 'lainnya',
            };

            return [
                'nama' => $item['Menu'] ?? '-',
                'kalori' => ($item['Energy (kJ)'] ?? 0) * 0.239,
                'protein' => $item['Protein (g)'] ?? 0,
                'lemak' => $item['Fat (g)'] ?? 0,
                'karbohidrat' => $item['Carbohydrates (g)'] ?? 0,
                'serat' => $item['Dietary Fiber (g)'] ?? 0,
                'kalsium' => $item['Calcium (mg)'] ?? 0,
                'vit_c' => $item['Vitamin C (mg)'] ?? $item['Vitamin C(mg)'] ?? 0,
                'zat_besi' => $item['Iron (mg)'] ?? $item['Iron(mg)'] ?? 0,

                    // cluster asli (untuk logika)
                    'cluster' => $cluster,

                    // untuk tampilan (biar 1–4, bukan 0–3)
                    'cluster_display' => $cluster + 1,

                    // kategori hasil interpretasi
                    'kategori' => $kategoriMap[$cluster] ?? 'Cluster ' . ($cluster + 1),

                    // label siap tampil
                    'cluster_label' => 'Cluster ' . ($cluster + 1) . ' - ' . ($kategoriMap[$cluster] ?? ''),
                    'jenis' => $jenis,
                    'kategori_data' => $kategori_data,
            ];
        }, $response->json());
        }

        return [];
    }

public function generateMBGMenu($userKategori)
{
    $foods = collect($this->getClusteredFoods());

    // filter makanan aneh
    $foods = $foods->filter(function ($item) {

        $nama = strtolower($item['nama']);

        if (
            str_contains($nama, 'mentah') ||
            str_contains($nama, 'minyak') ||
            str_contains($nama, 'bumbu')
        ) {
            return false;
        }

        return true;
    });

    // =========================
    // 🎯 KEBUTUHAN GIZI
    // =========================
    $kebutuhan = [
        'anak' => [
            'kalori' => 1675,
            'protein' => 39,
            'karbohidrat' => 255,
            'lemak' => 57,
            'serat' => 24,
            'zat_besi' => 9,
            'vit_c' => 47,
            'kalsium' => 1100
        ],

        'remaja' => [
            'kalori' => 2300,
            'protein' => 69,
            'karbohidrat' => 337,
            'lemak' => 76,
            'serat' => 32,
            'zat_besi' => 13,
            'vit_c' => 76,
            'kalsium' => 1200
        ],

        'ibu' => [
            'kalori' => 2513,
            'protein' => 78,
            'karbohidrat' => 393,
            'lemak' => 62,
            'serat' => 36,
            'zat_besi' => 31,
            'vit_c' => 103,
            'kalsium' => 2900
        ]
    ];

    $target = $kebutuhan[$userKategori];
// =========================
// 🍱 PAKET MENU FIX
// =========================
    $paketMenu = [

        [
            'nama' => 'Paket Menu 1',
            'gambar' => 'images/paket/paket1.png',
            'menu' => [
                'pokok' => 'Nasi Putih',
                'hewani' => 'Daging Ayam Goreng',
                'sayur' => 'Sayur Bayam',
                'nabati' => 'Tempe Goreng',
                'buah' => 'Pisang Ambon',
            ]
        ],

        [
            'nama' => 'Paket Menu 2',
            'gambar' => 'images/paket/paket2.png',
            'menu' => [
                'pokok' => 'Nasi Uduk',
                'hewani' => 'Ikan Goreng',
                'sayur' => 'Sayur Kangkung',
                'nabati' => 'Tahu Goreng',
                'buah' => 'Jeruk Manis',
            ]
        ],

        [
            'nama' => 'Paket Menu 3',
            'gambar' => 'images/paket/paket3.png',
            'menu' => [
                'pokok' => 'Nasi Tim Ayam',
                'hewani' => 'Telur Dadar',
                'sayur' => 'Sayur Sop',
                'nabati' => 'Tempe Bacem',
                'buah' => 'Pisang Kepok',
            ]
        ],

        [
            'nama' => 'Paket Menu 4',
            'gambar' => 'images/paket/paket4.png',
            'menu' => [
                'pokok' => 'Nasi Liwet',
                'hewani' => 'Ikan Bandeng',
                'sayur' => 'Sayur Sop',
                'nabati' => 'Tahu Goreng',
                'buah' => 'Jeruk Bali',
            ]
        ],

        [
            'nama' => 'Paket Menu 5',
            'gambar' => 'images/paket/paket5.png',
            'menu' => [
                'pokok' => 'Nasi Uduk',
                'hewani' => 'Daging Ayam Goreng',
                'sayur' => 'Sayur Kangkung',
                'nabati' => 'Tempe Goreng',
                'buah' => 'Pisang Kepok',
                'susu' => 'Ultramilk',
            ]
        ],

        [
            'nama' => 'Paket Menu 6',
            'gambar' => 'images/paket/paket6.png',
            'menu' => [
                'pokok' => 'Nasi Putih',
                'hewani' => 'Telur Dadar',
                'sayur' => 'Sayur Sop',
                'nabati' => 'Tahu Goreng',
                'buah' => 'Pisang Ambon',
            ]
        ],

        [
            'nama' => 'Paket Menu 7',
            'gambar' => 'images/paket/paket7.png',
            'menu' => [
                'pokok' => 'Nasi Liwet',
                'hewani' => 'Ikan Goreng',
                'sayur' => 'Sayur Sop',
                'nabati' => 'Tempe Goreng',
                'buah' => 'Jeruk Bali',
                'susu' => 'Susu Segar',
            ]
        ],

        [
            'nama' => 'Paket Menu 8',
            'gambar' => 'images/paket/paket8.png',
            'menu' => [
                'pokok' => 'Nasi Tim Ayam',
                'sayur' => 'Sayur Kangkung',
                'nabati' => 'Tahu Goreng',
                'buah' => 'Pisang Ambon',
            ]
        ],

    ];

    // =========================
    // 🧠 REKOMENDASI AI
    // =========================
    $getRekomendasi = function ($score) {

        if ($score <= 1500) {
            return [
                'label' => 'Sangat Baik',
                'target' => 'Kebutuhan Nutrisi Sangat Sesuai'
            ];
        }

        if ($score <= 2500) {
            return [
                'label' => 'Baik',
                'target' => 'Kebutuhan Nutrisi Sesuai'
            ];
        }

        return [
            'label' => 'Cukup',
            'target' => 'Masih Membutuhkan Tambahan Nutrisi'
        ];
    };

    $batasScore = [
        'anak' => 1800,
        'remaja' => 2300,
        'ibu' => 3000,
    ];

    // =========================
    // 🔥 GENERATE PAKET
    // =========================
    return collect($paketMenu)->map(function ($paket) use (
        $foods,
        $target,
        $getRekomendasi,
        $batasScore,
        $userKategori
    ) {

        $menu = [];

        foreach ($paket['menu'] as $key => $namaMenu) {

            $found = $foods->first(function ($f) use ($namaMenu) {

                return strtolower(trim($f['nama'])) == strtolower(trim($namaMenu));
            });

            // fallback kalau tidak ditemukan
            if (!$found) {

                $found = [
                    'nama' => $namaMenu,
                    'kalori' => 0,
                    'protein' => 0,
                    'karbohidrat' => 0,
                    'lemak' => 0,
                    'serat' => 0,
                    'zat_besi' => 0,
                    'vit_c' => 0,
                    'kalsium' => 0,
                ];
            }

            $menu[$key] = $found;
        }

        // =========================
        // 📊 TOTAL NUTRISI
        // =========================
        $total = [
            'kalori' => collect($menu)->sum('kalori'),
            'protein' => collect($menu)->sum('protein'),
            'karbohidrat' => collect($menu)->sum('karbohidrat'),
            'lemak' => collect($menu)->sum('lemak'),
            'serat' => collect($menu)->sum('serat'),
            'zat_besi' => collect($menu)->sum('zat_besi'),
            'vit_c' => collect($menu)->sum('vit_c'),
            'kalsium' => collect($menu)->sum('kalsium'),
        ];

        // =========================
        // 🧠 AI SCORING
        // =========================
        $score =
            abs($target['kalori'] - $total['kalori']) +
            abs($target['protein'] - $total['protein']) +
            abs($target['karbohidrat'] - $total['karbohidrat']) +
            abs($target['lemak'] - $total['lemak']) +
            abs($target['serat'] - $total['serat']) +
            abs($target['zat_besi'] - $total['zat_besi']) +
            abs($target['vit_c'] - $total['vit_c']) +
            abs($target['kalsium'] - $total['kalsium']);

        $rekomendasi = $getRekomendasi($score);

        if (
                $total['kalori'] < ($target['kalori'] * 0.45) ||
                $total['protein'] < ($target['protein'] * 0.45)
            ) {
                return null;
            }

        return [

            'gambar' => url($paket['gambar']),

            'hasil' => [
                'menu' => $menu,
                'total' => array_map(fn($v) => round($v,1), $total),
                'score' => round($score,1)
            ],

            'target' => $target,

            'rekomendasi' => $rekomendasi,
        ];

    })->filter()->values()->map(function ($item, $index) {

        $hariList = [
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat'
        ];

        $item['hari'] = $hariList[$index] ?? 'Hari';

        return $item;
    });
}

        // =========================
        // 👶 STUNTING (1 hari)
        // =========================
     public function generateStuntingMenu()
    {
        $foods = collect($this->getClusteredFoods());

        // =========================
        // 🔥 FILTER MAKANAN
        // =========================
        $foods = $foods->filter(function ($item) {

            $nama = strtolower($item['nama']);

            if (
                str_contains($nama, 'kopi') ||
                str_contains($nama, 'soda') ||
                str_contains($nama, 'coca') ||
                str_contains($nama, 'fanta') ||
                str_contains($nama, 'sprite') ||
                str_contains($nama, 'mentah') ||
                str_contains($nama, 'biskuit')
            ) {
                return false;
            }

            return true;
        });

        // =========================
        // 🎯 TARGET GIZI STUNTING
        // =========================
        $target = [

            'kalori' => 1400,
            'protein' => 35,
            'zat_besi' => 10,
            'kalsium' => 1000,
            'vit_c' => 45,
        ];

        // =========================
        // 🍱 GROUPING
        // =========================
        $group = $foods->groupBy('jenis');
        $getMenu = function ($group, $jenis, $nutrisi, $limit = 5) {

                if (!isset($group[$jenis])) {
                    return null;
                }

                return $group[$jenis]
                    ->filter(fn($i) => $i['kategori_data'] == 'makanan')
                    ->sortByDesc($nutrisi)
                    ->take($limit)
                    ->random();
        };
        // =========================
        // 🧠 PILIH MAKANAN TERBAIK
        // =========================

        // 🌅 SARAPAN
        $sarapan = [
            $getMenu($group, 'pokok', 'karbohidrat'),
            $getMenu($group, 'hewani', 'protein'),
            $getMenu($group, 'buah', 'vit_c'),
        ];

        // 🍛 SIANG
        $siang = [
            $getMenu($group, 'pokok', 'karbohidrat'),
            $getMenu($group, 'hewani', 'protein'),
            $getMenu($group, 'sayur', 'zat_besi'),
            $getMenu($group, 'buah', 'vit_c'),
        ];

        // 🌙 MALAM
        $malam = [
            $getMenu($group, 'pokok', 'karbohidrat'),
            $getMenu($group, 'hewani', 'protein'),
            $getMenu($group, 'sayur', 'kalsium'),
        ];

        // =========================
        // 📊 TOTAL NUTRISI
        // =========================
        $menuAll = collect([
            ...$sarapan,
            ...$siang,
            ...$malam
        ])->filter();

        $total = [

            'kalori' => round($menuAll->sum('kalori'),1),
            'protein' => round($menuAll->sum('protein'),1),
            'zat_besi' => round($menuAll->sum('zat_besi'),1),
            'kalsium' => round($menuAll->sum('kalsium'),1),
            'vit_c' => round($menuAll->sum('vit_c'),1),
        ];

        // =========================
        // 🧠 AI SCORING
        // =========================
        $score =
            abs($target['kalori'] - $total['kalori']) +
            abs($target['protein'] - $total['protein']) +
            abs($target['zat_besi'] - $total['zat_besi']) +
            abs($target['kalsium'] - $total['kalsium']) +
            abs($target['vit_c'] - $total['vit_c']);

        // =========================
        // 🏆 LABEL HASIL
        // =========================
        if ($score <= 1000) {

            $status = 'Sangat Baik';

        } elseif ($score <= 1800) {

            $status = 'Baik';

        } else {

            $status = 'Cukup';
        }

        return [

            'sarapan' => $sarapan,
            'siang' => $siang,
            'malam' => $malam,

            'total' => $total,

            'target' => $target,

            'score' => round($score,1),

            'status' => $status
        ];
    }

        // =========================
        // 🔧 HELPER
        // =========================
        private function ambil($group, $key)
    {
        if (!isset($group[$key])) return null;

        return $group[$key]->shuffle()->first();
    }

    public function getStats()
    {
        $foods = $this->getClusteredFoods();

        $total = count($foods);

        // ambil cluster unik
        $clusters = array_unique(array_column($foods, 'cluster'));

        return [
            'total_makanan' => $total,
            'total_cluster' => count($clusters),
            'avg_kalori' => $total ? round(array_sum(array_column($foods,'kalori')) / $total, 1) : 0,
            'avg_protein' => $total ? round(array_sum(array_column($foods,'protein')) / $total, 1) : 0,
        ];
    }
}