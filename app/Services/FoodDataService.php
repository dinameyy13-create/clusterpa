<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FoodDataService
{
    public function getClusteredFoods()
    {
        return Cache::remember(
            'clustered_foods',
            300,
            function () {

                try {

                    $response = Http::timeout(60)
                        ->get(env('FLASK_API') . '/cluster');

                    if (!$response->successful()) {
                        return [];
                    }

                    return array_map(function ($item) {

                        $cluster = (int) ($item['cluster'] ?? 0);

                        $kategoriMap = [
                            0 => 'Seimbang',
                            1 => 'Tinggi Karbohidrat',
                            2 => 'Rendah Nutrisi',
                            3 => 'Tinggi Energi & Protein',
                        ];

                        $nama = strtolower(trim($item['Menu'] ?? '-'));

                        $isBahan = false;

                       if (

                            // kondisi umum
                            str_contains($nama, 'mentah') ||
                            str_contains($nama, 'segar') ||
                            str_contains($nama, 'kering') ||
                            str_contains($nama, 'asin') ||
                            str_contains($nama, 'bubuk') ||

                            // bahan pokok
                            str_contains($nama, 'beras') ||
                            str_contains($nama, 'tepung') ||
                            str_contains($nama, 'gula') ||
                            str_contains($nama, 'garam') ||
                            str_contains($nama, 'minyak') ||
                            str_contains($nama, 'margarin') ||
                            str_contains($nama, 'santan') ||
                            str_contains($nama, 'hunkwe') ||

                            // bagian hewan
                            str_contains($nama, 'gajih') ||
                            str_contains($nama, 'hati') ||
                            str_contains($nama, 'kulit') ||

                            // rempah & bumbu
                            str_contains($nama, 'laos') ||
                            str_contains($nama, 'lengkuas') ||
                            str_contains($nama, 'jahe') ||
                            str_contains($nama, 'kunyit') ||
                            str_contains($nama, 'kencur') ||
                            str_contains($nama, 'serai') ||
                            str_contains($nama, 'kemiri') ||
                            str_contains($nama, 'ketumbar') ||
                            str_contains($nama, 'lada') ||
                            str_contains($nama, 'merica') ||
                            str_contains($nama, 'cabe') ||
                            str_contains($nama, 'cabai') ||
                            str_contains($nama, 'bawang') ||
                            str_contains($nama, 'bumbu') ||
                            str_contains($nama, 'vetsin') ||
                            str_contains($nama, 'msg') ||

                            // rempah lain
                            str_contains($nama, 'kayu manis') ||
                            str_contains($nama, 'cengkeh') ||
                            str_contains($nama, 'pala') ||
                            str_contains($nama, 'kapulaga') ||

                            // tanaman/bagian tanaman
                            str_contains($nama, 'daun') ||
                            str_contains($nama, 'biji') ||
                            str_contains($nama, 'empon') ||
                            str_contains($nama, 'kunci')

                        ) {

                            $isBahan = true;
                        }

                        return [

                            'nama' => $item['Menu'] ?? '-',

                            'kalori' =>
                                ($item['Energy (kJ)'] ?? 0) * 0.239,

                            'protein' =>
                                $item['Protein (g)'] ?? 0,

                            'lemak' =>
                                $item['Fat (g)'] ?? 0,

                            'karbohidrat' =>
                                $item['Carbohydrates (g)'] ?? 0,

                            'serat' =>
                                $item['Dietary Fiber (g)'] ?? 0,

                            'cluster' => $cluster,

                            'cluster_display' => $cluster + 1,

                            'kategori' =>
                                $kategoriMap[$cluster]
                                ?? 'Cluster',

                            'kategori_data' =>
                                $isBahan ? 'bahan' : 'makanan',

                        ];

                    }, $response->json());

                } catch (\Exception $e) {

                    return [];
                }
            }
        );
    }

    public function generateMBGMenu($userKategori)
    {
        $foods = collect($this->getClusteredFoods());

        // =========================
        // KEBUTUHAN GIZI
        // =========================
        $kebutuhan = [

            'paud' => [
                'kalori' => 1400,
                'protein' => 25,
                'karbohidrat' => 220,
                'lemak' => 50,
                'serat' => 20,
                'zat_besi' => 10,
                'vit_c' => 45,
                'kalsium' => 1000,
            ],

            'sd' => [
                'kalori' => 1950,
                'protein' => 53,
                'karbohidrat' => 290,
                'lemak' => 65,
                'serat' => 28,
                'zat_besi' => 8,
                'vit_c' => 50,
                'kalsium' => 1200,
            ],

            'smp' => [
                'kalori' => 2225,
                'protein' => 68,
                'karbohidrat' => 325,
                'lemak' => 75,
                'serat' => 32,
                'zat_besi' => 13,
                'vit_c' => 70,
                'kalsium' => 1200,
            ],

            'sma' => [
                'kalori' => 2375,
                'protein' => 70,
                'karbohidrat' => 350,
                'lemak' => 78,
                'serat' => 33,
                'zat_besi' => 13,
                'vit_c' => 83,
                'kalsium' => 1200,
            ],

            'ibu' => [
                'kalori' => 2513,
                'protein' => 78,
                'karbohidrat' => 393,
                'lemak' => 62,
                'serat' => 36,
                'zat_besi' => 31,
                'vit_c' => 103,
                'kalsium' => 2900,
            ],

        ];

        $target = $kebutuhan[$userKategori];

        // =========================
        // PORSI BERDASARKAN USER
        // diasumsikan data nutrisi = per 100g
        // =========================

        $porsi = [

            'paud' => [
                'pokok' => 1.5,
                'hewani' => 1,
                'nabati' => 1,
                'sayur' => 1,
                'buah' => 1,
                'susu' => 1,
            ],

            'sd' => [
                'pokok' => 2,
                'hewani' => 1.5,
                'nabati' => 1,
                'sayur' => 1,
                'buah' => 1,
                'susu' => 1,
            ],

            'smp' => [
                'pokok' => 2.5,
                'hewani' => 2,
                'nabati' => 1.5,
                'sayur' => 1.5,
                'buah' => 1,
                'susu' => 1,
            ],

            'sma' => [
                'pokok' => 3,
                'hewani' => 2,
                'nabati' => 1.5,
                'sayur' => 1.5,
                'buah' => 1,
                'susu' => 1.5,
            ],

            'ibu' => [
                'pokok' => 3,
                'hewani' => 2.5,
                'nabati' => 2,
                'sayur' => 2,
                'buah' => 1.5,
                'susu' => 2,
            ],

        ];

        $currentPorsi = $porsi[$userKategori];

        // =========================
        // LIST SUSU
        // =========================
        $susu = [
            'Susu Ultramilk',
            'Ultramilk Coklat',
            'Susu segar',
            'Susu Kedelai',
            'Cimory Fresh Milk Cashew',
            'jus mannga',
            'jus alpukat',
            'susu milo/ milo choklat',

        ];

        // =========================
        // TEMPLATE MENU
        // =========================
        $paketMenu = [

            [
                'pokok' => 'Nasi Uduk',

                'hewani' => [
                    'Ikan Bandeng',
                    'Daging Ayam Goreng',
                    'Ikan Goreng',
                    'Ikan Mas Goreng',
                    'Ikan Pindang Layang Goreng',
                    'Cumi-cumi Goreng',
                    'Bebek Goreng',
                    'Chicken Nugget',
                    'Ayam Goreng Kalasan Paha',
                    'Beef Teriyaki',
                    'Beef Yakiniku',
                    'Telur ceplok',
                    'Telur dadar',
                ],

                'nabati' => [
                    'Tempe bacem',
                    'Tempe Goreng',
                    'Tahu Goreng',
                    'Tahu Bakso',
                ],

                'sayur' => [
                    'Sayur Bayam',
                    'Sayur Kangkung',
                    'Sayur Sop',
                    'Sayur Soun',
                    'Sayur Sop macaroni',
                    'Sayur bayam wortel',
                    'Sup ayam dan kentang',
                    'oseng oseng kol',
                    'Sayur lodeh',
                    'Sayur asem',
                ],
            ],

            [
                'pokok' => 'Spaghetti',

                'hewani' => [
                    'Chicken Teriyaki',
                    'Beef Yakiniku',
                    'Beef Burger',
                ],

                'nabati' => [
                    null
                ],

                'sayur' => [
                    'Capcay sayur',
                ],
            ],

            [
                'pokok' => 'Nasi Tim Ayam',

                'hewani' => [
                    null
                ],

                'nabati' => [
                    'Tempe bacem',
                    'Tempe Goreng',
                    'Tahu Goreng',
                    'Tahu Bakso',
                ],

                'sayur' => [
                    'Capcay Sayur',
                    'Sayur Bayam',
                    'Sayur Kangkung',
                    'Sayur Sop',
                    'Sayur Soun',
                    'Sayur Sop macaroni',
                    'Sayur bayam wortel',
                    'Sup ayam dan kentang',
                    'oseng oseng kol',
                    'Sayur lodeh',
                    'Sayur asem',
                ],
            ],

            [
                'pokok' => 'Nasi Putih',

                'hewani' => [
                    'Ikan Bandeng',
                    'Daging Ayam Goreng',
                    'Ikan Goreng',
                    'Ikan Mas Goreng',
                    'Ikan Pindang Layang Goreng',
                    'Cumi-cumi Goreng',
                    'Bebek Goreng',
                    'Chicken Nugget',
                    'Ayam Goreng Kalasan Paha',
                    'Beef Teriyaki',
                    'Beef Yakiniku',
                    'Telur ceplok',
                    'Telur dadar',

                ],

                'nabati' => [
                    'Tempe bacem',
                    'Tempe Goreng',
                    'Tahu Goreng',
                    'Tahu Bakso',
                ],

                'sayur' => [
                    'Sayur Bayam',
                    'Sayur Kangkung',
                    'Sayur Sop',
                    'Sayur Soun',
                    'Sayur Sop macaroni',
                    'Sayur bayam wortel',
                    'Sup ayam dan kentang',
                    'oseng oseng kol',
                    'Sayur lodeh',
                    'Sayur asem',
                ],
            ],

            [
                'pokok' => 'Nasi Liwet',

                'hewani' => [
                    'Ikan Bandeng',
                    'Daging Ayam Goreng',
                    'Ikan Goreng',
                    'Ikan Mas Goreng',
                    'Ikan Pindang Layang Goreng',
                    'Cumi-cumi Goreng',
                    'Bebek Goreng',
                    'Ayam Goreng Kalasan Paha',
                    'Telur ceplok',
                    'Telur dadar',
                ],

                'nabati' => [
                    'Tempe bacem',
                    'Tempe Goreng',
                    'Tahu Goreng',
                    'Tahu Bakso',
                ],

                'sayur' => [
                    'Sayur Kangkung',
                    'Sayur Bayam',
                    'Sayur Sop',
                    'Sayur Soun',
                    'Sayur Sop macaroni',
                    'Sayur bayam wortel',
                    'Sup ayam dan kentang',
                    'oseng oseng kol',
                ],
            ],

            [
                'pokok' => 'Nasi Merah',

                'hewani' => [
                    'Chicken Teriyaki',
                    'Ikan Bandeng',
                    'Daging Ayam Goreng',
                    'Ikan Goreng',
                    'Ikan Mas Goreng',
                    'Ikan Pindang Layang Goreng',
                    'Cumi-cumi Goreng',
                    'Bebek Goreng',
                    'Chicken Nugget',
                    'Ayam Goreng Kalasan Paha',
                    'Telur ceplok',
                    'Telur dadar',
                    'telur putih rebus',
                ],

                'nabati' => [
                    'Tempe bacem',
                    'Tempe Goreng',
                    'Tahu Goreng',
                ],

                'sayur' => [
                    'Capcay Sayur',
                    'Sayur Bayam',
                    'Sayur Kangkung',
                    'Sayur Sop',
                    'Sayur Soun',
                    'Sayur Sop macaroni',
                    'Sayur bayam wortel',
                    'Sup ayam dan kentang',
                    'oseng oseng kol',
                    'Sayur asem',
                ],
            ],

            [
                'pokok' => 'Bihun Goreng',

                'hewani' => [
                    'Ikan Bandeng',
                    'Daging Ayam Goreng',
                    'Ikan Goreng',
                    'Ikan Mas Goreng',
                    'Ikan Pindang Layang Goreng',
                    'Cumi-cumi Goreng',
                    'Bebek Goreng',
                    'Chicken Nugget',
                    'Ayam Goreng Kalasan Paha',
                    'Telur ceplok',
                    'Telur dadar',
                ],

                'nabati' => [
                    'Tempe bacem',
                    'Tempe Goreng',
                    'Tahu Goreng',
                    'Tahu Bakso',
                ],

                'sayur' => [
                    'Capcay sayur',
                    'Sayur Bayam',
                    'Sayur Kangkung',
                    'Sayur Sop',
                    'Sayur bayam wortel',
                    'Sup ayam dan kentang',
                    'oseng oseng kol',
                    'Sayur lodeh',
                    'Sayur asem',
                ],
            ],

        ];

        // =========================
        // LIST BUAH RANDOM
        // =========================
        $buahRandom = [

            'Pisang Ambon',
            'Pisang Kepok',
            'Jeruk Manis',
            'Jeruk Bali',
            'Apel',
            'Apel Malang',
            'Buah Naga Merah',
            'kurma',
            'kelengkeng',
            'jambu air',
            'manggis',
            'semangka',
            'sawo',
            'rambutan',
            'salak',
        ];

        // =========================
        // AMBIL DATA MAKANAN
        // =========================
        $ambilMakanan = function ($nama) use ($foods) {

            $found = $foods->first(function ($f) use ($nama) {

                return strtolower(trim($f['nama'])) ==
                    strtolower(trim($nama));
            });

            if (!$found) {

                return [
                    'nama' => $nama,
                    'kalori' => 0,
                    'protein' => 0,
                    'karbohidrat' => 0,
                    'lemak' => 0,
                    'serat' => 0,
                ];
            }

            // gambar makanan
            $gambar = \DB::table('food_images')
                ->whereRaw(
                    'LOWER(nama_makanan) = ?',
                    [strtolower($nama)]
                )
                ->value('gambar');

            $found['gambar'] = $gambar
                ? asset('images/Makanan/' . $gambar)
                : asset('images/default-food.png');

            return $found;
        };

        // =========================
        // GENERATE MENU
        // =========================
        $hasil = [];

        for ($i = 0; $i < 5; $i++) {

            // ambil template random
            $template =
                $paketMenu[array_rand($paketMenu)];

            $menu = [];

            foreach ($template as $kategori => $namaMakanan) {

                // =========================
                // POKOK (STRING)
                // =========================
                if ($kategori == 'pokok') {

                    $menu[$kategori] =
                        $ambilMakanan($namaMakanan);

                    // =========================
                    // HITUNG PORSI
                    // =========================
                    $multiplier =
                        $currentPorsi[$kategori] ?? 1;

                    $menu[$kategori]['porsi'] =
                        $multiplier;

                    $menu[$kategori]['kalori'] *=
                        $multiplier;

                    $menu[$kategori]['protein'] *=
                        $multiplier;

                    $menu[$kategori]['karbohidrat'] *=
                        $multiplier;

                    $menu[$kategori]['lemak'] *=
                        $multiplier;

                    $menu[$kategori]['serat'] *=
                        $multiplier;

                    continue;
                }

                // =========================
                // RANDOM PILIHAN ARRAY
                // =========================
                if (!is_array($namaMakanan)) {
                    continue;
                }

                $pilihan =
                    $namaMakanan[array_rand($namaMakanan)];

                // skip kalau null
                if ($pilihan === null) {
                    continue;
                }

                $menu[$kategori] =
                    $ambilMakanan($pilihan);

                // =========================
                // HITUNG PORSI
                // =========================
                $multiplier =
                    $currentPorsi[$kategori] ?? 1;

                $menu[$kategori]['porsi'] =
                    $multiplier;

                $menu[$kategori]['kalori'] *=
                    $multiplier;

                $menu[$kategori]['protein'] *=
                    $multiplier;

                $menu[$kategori]['karbohidrat'] *=
                    $multiplier;

                $menu[$kategori]['lemak'] *=
                    $multiplier;

                $menu[$kategori]['serat'] *=
                    $multiplier;
            }

            // =========================
            // TAMBAH BUAH RANDOM
            // =========================
            $menu['buah'] =
                $ambilMakanan(
                    $buahRandom[array_rand($buahRandom)]
                );

            // =========================
            // TOTAL NUTRISI AWAL
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
            // TAMBAH SUSU JIKA PROTEIN RENDAH
            // =========================
            if ($total['protein'] < 35) {

                $menu['susu'] =
                    $ambilMakanan(
                        $susu[array_rand($susu)],
                    );

                 // hitung ulang nutrisi
                $total = [

                    'kalori' =>
                        collect($menu)->sum('kalori'),

                    'protein' =>
                        collect($menu)->sum('protein'),

                    'karbohidrat' =>
                        collect($menu)->sum('karbohidrat'),

                    'lemak' =>
                        collect($menu)->sum('lemak'),

                    'serat' =>
                        collect($menu)->sum('serat'),

                    'zat_besi' =>
                        collect($menu)->sum('zat_besi'),

                    'vit_c' =>
                        collect($menu)->sum('vit_c'),

                    'kalsium' =>
                        collect($menu)->sum('kalsium'),
                ];
            }

            // =========================
            // SCORING
            // =========================
            $score =

                abs($target['kalori'] - $total['kalori']) +

                abs($target['protein'] - $total['protein']) +

                abs($target['karbohidrat'] - $total['karbohidrat']) +

                abs($target['lemak'] - $total['lemak']) +

                abs($target['serat'] - $total['serat']);

            // =========================
            // LABEL REKOMENDASI
            // =========================
            if ($score <= 1500) {

                $label = 'Sangat Baik';

            } elseif ($score <= 2500) {

                $label = 'Baik';

            } else {

                $label = 'Cukup';
            }

            $hariList = [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat'
            ];

            $hasil[] = [

                'hari' => $hariList[$i],

                'hasil' => [

                    'menu' => $menu,

                    'total' => array_map(function ($v) {
                        return round($v, 1);
                    }, $total),

                    'score' => round($score, 1),
                ],

                'target' => $target,

                'rekomendasi' => [
                    'label' => $label
                ],
            ];
        }

        return collect($hasil);
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