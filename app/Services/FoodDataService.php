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

            return [
                'nama' => $item['Menu'] ?? '-',
                'kalori' => $item['Energy (kJ)'] ?? 0,
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
            ];
        }, $response->json());
        }

        return [];
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