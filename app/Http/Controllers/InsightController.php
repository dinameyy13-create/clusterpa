<?php

namespace App\Http\Controllers;

use App\Services\FoodDataService;

class InsightController extends Controller
{
    public function __construct(private FoodDataService $foodService) {}

    public function index()
    {
        $foods = $this->foodService->getClusteredFoods();

        $clusters = [];

        for ($i = 1; $i <= 4; $i++) {

            // Ambil data berdasarkan cluster
            $clusterFoods = array_values(array_filter(
                $foods,
                fn($f) => ($f['cluster_display'] ?? 0) === $i
            ));

            $foodsCollect = collect($clusterFoods);

            // Rata-rata nutrisi
            $avgKalori = round($foodsCollect->avg('kalori') ?? 0);
            $avgProtein = round($foodsCollect->avg('protein') ?? 0, 1);
            $avgKarbo = round($foodsCollect->avg('karbohidrat') ?? 0, 1);
            $avgLemak = round($foodsCollect->avg('lemak') ?? 0, 1);
            $avgSerat = round($foodsCollect->avg('serat') ?? 0, 1);

            // Nama kategori cluster
            $kategori = $clusterFoods[0]['kategori'] ?? ('Cluster ' . $i);

            $menuKeywords = [
                'nasi',
                'bubur',
                'sup',
                'soto',
                'sayur',
                'capcay',
                'tumis',
                'semur',
                'rawon',
                'gulai',
                'rendang',
                'bakso',
                'mie',
                'mi ',
                'omelet',
                'telur',
                'ayam',
                'ikan',
                'daging',
                'pepes',
                'tim',
                'sate',
                'perkedel',
                'lontong',
                'gado',
                'pecel',
                'sop',
            ];

            $bestFoods = collect($clusterFoods)

                // hanya makanan
                ->where('kategori_data', 'makanan')

                // prioritaskan makanan siap saji
                ->filter(function ($food) use ($menuKeywords) {

                    $nama = strtolower($food['nama']);

                    foreach ($menuKeywords as $keyword) {
                        if (str_contains($nama, $keyword)) {
                            return true;
                        }
                    }

                    return false;
                })

                // buang bahan/olahan yang kurang representatif
                ->reject(function ($food) {

                    $nama = strtolower($food['nama']);

                    return
                        str_contains($nama, 'asin') ||
                        str_contains($nama, 'kering') ||
                        str_contains($nama, 'bubuk') ||
                        str_contains($nama, 'vetsin') ||
                        str_contains($nama, 'msg');
                })

                // urutkan berdasarkan skor nutrisi
                ->sortByDesc(function ($f) {

                    return
                        (($f['protein'] ?? 0) * 2) +
                        (($f['serat'] ?? 0) * 1.5) +
                        (($f['karbohidrat'] ?? 0) * 0.5);
                })

                ->take(8)
                ->values();

            if ($bestFoods->isEmpty()) {

                $bestFoods = collect($clusterFoods)

                    ->where('kategori_data', 'makanan')

                    ->take(8)

                    ->values();
            }

            if ($bestFoods->isEmpty()) {

                $bestFoods = collect($clusterFoods)

                    ->take(8)

                    ->values();
            }

            // ===========================
            // Karakteristik cluster
            // ===========================
            $karakteristik = [];

            if ($avgProtein >= 15) {
                $karakteristik[] = 'Protein tinggi';
            }

            if ($avgKarbo >= 30) {
                $karakteristik[] = 'Karbohidrat tinggi';
            }

            if ($avgLemak >= 15) {
                $karakteristik[] = 'Lemak relatif tinggi';
            }

            if ($avgSerat >= 5) {
                $karakteristik[] = 'Serat tinggi';
            }

            if ($avgKalori >= 300) {
                $karakteristik[] = 'Energi tinggi';
            }

            if (empty($karakteristik)) {
                $karakteristik[] = 'Komposisi nutrisi relatif seimbang';
            }

            // ===========================
            // Peran utama berdasarkan kategori
            // ===========================
            switch ($kategori) {

                case 'Seimbang':
                    $peranUtama = 'Memiliki komposisi nutrisi yang relatif seimbang sehingga cocok sebagai pilihan menu harian.';
                    break;

                case 'Tinggi Karbohidrat':
                    $peranUtama = 'Didominasi kandungan karbohidrat sehingga dapat menjadi sumber energi utama untuk aktivitas sehari-hari.';
                    break;

                case 'Rendah Nutrisi':
                    $peranUtama = 'Memiliki kandungan gizi yang relatif rendah sehingga sebaiknya dikombinasikan dengan makanan bergizi lainnya.';
                    break;

                case 'Tinggi Energi & Protein':
                    $peranUtama = 'Mengandung energi dan protein yang relatif tinggi sehingga baik untuk mendukung pertumbuhan dan perbaikan jaringan tubuh.';
                    break;

                default:
                    $peranUtama = 'Memiliki karakteristik nutrisi tertentu berdasarkan hasil clustering.';
                    break;
            }

            // ===========================
            // Simpan data cluster
            // ===========================
            $clusters[] = [

                'id' => $i,

                'label' => $kategori,

                'label_full' => 'Cluster ' . $i . ' - ' . $kategori,

                'description' =>
                    "Rata-rata {$avgKalori} kcal, {$avgProtein} g protein, {$avgKarbo} g karbohidrat, {$avgLemak} g lemak, dan {$avgSerat} g serat.",

                'foods' => $bestFoods,

                'count' => count($clusterFoods),

                'avg_kalori' => $avgKalori,
                'avg_protein' => $avgProtein,
                'avg_karbohidrat' => $avgKarbo,
                'avg_lemak' => $avgLemak,
                'avg_serat' => $avgSerat,

                'karakteristik' => $karakteristik,

                'peran_utama' => $peranUtama,
            ];
        }

        return view('insight', compact('clusters'));
    }
}