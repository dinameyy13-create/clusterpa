<?php

namespace App\Http\Controllers;

use App\Services\FoodDataService;

class DashboardController extends Controller
{
    public function __construct(private FoodDataService $foodService) {}

    public function landing()
    {
        return view('landingpage');
    }

    public function index()
    {
        $stats = $this->foodService->getStats();
        $foods = $this->foodService->getClusteredFoods();

        $clusterInfo = [];

        $clusterColors = [
            '#2563EB',
            '#F59E0B',
            '#10B981',
            '#8B5CF6'
        ];

        $clusterLabels = [
            0 => 'Protein Sedang',
            1 => 'Tinggi Energi Lengkap',
            2 => 'Tinggi Karbohidrat',
            3 => 'Seimbang',
        ];

        for ($i = 0; $i < 4; $i++) {

            $clusterFoods = array_filter(
                $foods,
                fn($f) => $f['cluster'] === $i
            );

            $count = count($clusterFoods);

            $avgProtein =
                $count > 0
                ? round(array_sum(array_column($clusterFoods, 'protein')) / $count, 1)
                : 0;

            $avgKarbo =
                $count > 0
                ? round(array_sum(array_column($clusterFoods, 'karbohidrat')) / $count, 1)
                : 0;

            $avgKalori =
                $count > 0
                ? round(array_sum(array_column($clusterFoods, 'kalori')) / $count, 1)
                : 0;

            $sampleFoods = array_slice(
                array_column($clusterFoods, 'nama'),
                0,
                4
            );

            $clusterInfo[] = [
                'label' => $clusterLabels[$i],
                'color' => $clusterColors[$i],
                'count' => $count,
                'avg_protein' => $avgProtein,
                'avg_karbo' => $avgKarbo,
                'avg_kalori' => $avgKalori,
                'samples' => $sampleFoods,
            ];
        }

        return view('dashboard', compact(
            'stats',
            'foods',
            'clusterInfo'
        ));
    }
}