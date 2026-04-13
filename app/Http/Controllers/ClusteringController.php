<?php

namespace App\Http\Controllers;

use App\Services\FoodDataService;
use Illuminate\Http\Request;

class ClusteringController extends Controller
{
    public function __construct(private FoodDataService $foodService) {}

    public function index()
    {
        $foods = $this->foodService->getClusteredFoods();

        // Hitung stats
        $total_makanan = count($foods);
        $total_cluster = 4; // Karena K-Means k=4
        $avg_kalori = $total_makanan ? round(array_sum(array_column($foods,'kalori')) / $total_makanan, 1) : 0;
        $avg_protein = $total_makanan ? round(array_sum(array_column($foods,'protein')) / $total_makanan, 1) : 0;

        $stats = [
            'total_makanan' => $total_makanan,
            'total_cluster' => $total_cluster,
            'avg_kalori' => $avg_kalori,
            'avg_protein' => $avg_protein,
        ];

        // Distribusi cluster untuk chart
        $clusterDistribution = [];
        $clusterColors = ['#2563EB','#F59E0B','#10B981','#8B5CF6'];
        $clusterLabels = ['Tinggi Protein','Tinggi Karbohidrat','Seimbang & Bergizi','Tinggi Serat & Vitamin'];

        for ($i=0; $i<$total_cluster; $i++) {
            $count = count(array_filter($foods, fn($f) => $f['cluster'] == $i));
            $clusterDistribution[] = [
                'label' => $clusterLabels[$i],
                'count' => $count,
                'color' => $clusterColors[$i],
            ];
        }

        return view('clustering', compact('foods', 'stats', 'clusterDistribution'));
    }

    public function getData(Request $request)
    {
        $foods = $this->foodService->getClusteredFoods();

        if ($request->filled('cluster') && $request->cluster !== 'all') {
            $foods = array_filter($foods, fn($f) => $f['cluster'] == $request->cluster);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $foods = array_filter($foods, fn($f) => str_contains(strtolower($f['nama']), $search));
        }

        return response()->json(array_values($foods));
    }

    public function getGrafikData()
    {
        $foods = $this->foodService->getClusteredFoods();

        // SCATTER DATA
        $scatter = array_map(function ($f) {
            return [
                'x' => $f['protein'],
                'y' => $f['kalori'],
                'cluster' => $f['cluster'],
                'nama' => $f['nama']
            ];
        }, $foods);

        // BAR DATA (rata-rata per cluster)
        $clusters = [];

        foreach ($foods as $f) {
            $c = $f['cluster'];

            if (!isset($clusters[$c])) {
                $clusters[$c] = [
                    'cluster' => 'Cluster ' . ($c + 1),
                    'protein' => 0,
                    'karbohidrat' => 0,
                    'lemak' => 0,
                    'serat' => 0,
                    'zat_besi' => 0,
                    'kalsium' => 0,
                    'kalori' => 0,
                    'count' => 0,
                ];
            }

            $clusters[$c]['protein'] += $f['protein'];
            $clusters[$c]['karbohidrat'] += $f['karbohidrat'];
            $clusters[$c]['lemak'] += $f['lemak'];
            $clusters[$c]['serat'] += $f['serat'] ?? 0;
            $clusters[$c]['zat_besi'] += $f['zat_besi'] ?? 0;
            $clusters[$c]['kalsium'] += $f['kalsium'] ?? 0;
            $clusters[$c]['kalori'] += $f['kalori'];
            $clusters[$c]['count']++;
        }

        // HITUNG RATA-RATA
        $bar = array_map(function ($c) {
            foreach (['protein','karbohidrat','lemak','serat','zat_besi','kalsium','kalori'] as $k) {
                $c[$k] = $c['count'] ? round($c[$k] / $c['count'], 2) : 0;
            }
            return $c;
        }, $clusters);

        return response()->json([
            'scatter' => array_values($scatter),
            'bar' => array_values($bar)
        ]);
    }
}