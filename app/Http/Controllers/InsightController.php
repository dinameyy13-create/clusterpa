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

        // loop sesuai display (1–4)
        for ($i = 1; $i <= 4; $i++) {

            // ambil berdasarkan cluster_display
            $clusterFoods = array_values(array_filter(
                $foods,
                fn($f) => $f['cluster_display'] === $i
            ));

            $foodsCollect = collect($clusterFoods);

            $avgKalori = $foodsCollect->avg('kalori') ?? 0;
            $avgProtein = $foodsCollect->avg('protein') ?? 0;

            // ambil kategori dari service (INI KUNCI NYA 🔥)
            $kategori = $clusterFoods[0]['kategori'] ?? 'Cluster '.$i;

            // ambil label lengkap kalau mau
            $labelFull = $clusterFoods[0]['cluster_label'] ?? ('Cluster '.$i.' - '.$kategori);

            // ambil TOP makanan terbaik (ranking sederhana)
            $bestFoods = $foodsCollect
                ->sortByDesc(fn($f) => ($f['protein'] * 2) + ($f['serat']) + ($f['vit_c']))
                ->take(8)
                ->values();

            $clusters[] = [
                'id' => $i,

                // 🔥 pakai dari service
                'label' => $clusterFoods[0]['kategori'] ?? 'Cluster '.$i,
                'label_full' => $labelFull,

                'description' => 'Kelompok makanan dengan karakteristik '.$kategori,

                'foods' => $bestFoods,
                'count' => count($clusterFoods),

                // insight
                'avg_kalori' => round($avgKalori),
                'avg_protein' => round($avgProtein, 1),

                'peran_utama' => match($i) {
                    1 => 'Menyeimbangkan kebutuhan nutrisi harian',
                    2 => 'Menyediakan energi utama untuk aktivitas',
                    3 => 'Perlu dikontrol karena rendah nutrisi',
                    4 => 'Mendukung pertumbuhan dan energi tinggi',
                    default => '-'
                },
            ];
        }

        return view('insight', compact('clusters'));
    }
}