<?php

namespace App\Services;

class KMeansService
{
    private int $k;
    private int $maxIterations;

    public function __construct(int $k = 4, int $maxIterations = 100)
    {
        $this->k = $k;
        $this->maxIterations = $maxIterations;
    }

    public function cluster(array $data): array
    {
        if (empty($data)) return [];

        // Normalize features
        $features = ['kalori', 'protein', 'karbohidrat', 'lemak', 'serat', 'zat_besi', 'kalsium', 'vitamin_c'];
        $normalized = $this->normalize($data, $features);

        // Initialize centroids randomly
        $centroids = $this->initializeCentroids($normalized, $features);

        $assignments = array_fill(0, count($data), 0);

        for ($iter = 0; $iter < $this->maxIterations; $iter++) {
            $newAssignments = [];

            foreach ($normalized as $idx => $point) {
                $minDist = PHP_FLOAT_MAX;
                $bestCluster = 0;

                foreach ($centroids as $k => $centroid) {
                    $dist = $this->euclideanDistance($point, $centroid, $features);
                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $bestCluster = $k;
                    }
                }
                $newAssignments[$idx] = $bestCluster;
            }

            if ($newAssignments === $assignments) break;
            $assignments = $newAssignments;

            // Update centroids
            $centroids = $this->updateCentroids($normalized, $assignments, $features);
        }

        // Assign cluster labels to original data
        foreach ($data as $idx => &$item) {
            $item['cluster'] = $assignments[$idx];
            $item['cluster_label'] = $this->getClusterLabel($assignments[$idx]);
            $item['cluster_color'] = $this->getClusterColor($assignments[$idx]);
        }

        return $data;
    }

    private function normalize(array $data, array $features): array
    {
        $mins = [];
        $maxs = [];

        foreach ($features as $feature) {
            $values = array_column($data, $feature);
            $mins[$feature] = min($values);
            $maxs[$feature] = max($values);
        }

        $normalized = [];
        foreach ($data as $idx => $item) {
            $normalized[$idx] = [];
            foreach ($features as $feature) {
                $range = $maxs[$feature] - $mins[$feature];
                $normalized[$idx][$feature] = $range > 0
                    ? ($item[$feature] - $mins[$feature]) / $range
                    : 0;
            }
        }

        return $normalized;
    }

    private function initializeCentroids(array $data, array $features): array
    {
        $n = count($data);
        $step = (int)($n / $this->k);
        $centroids = [];

        for ($k = 0; $k < $this->k; $k++) {
            $idx = min($k * $step, $n - 1);
            $centroids[$k] = $data[$idx];
        }

        return $centroids;
    }

    private function euclideanDistance(array $a, array $b, array $features): float
    {
        $sum = 0;
        foreach ($features as $feature) {
            $sum += pow(($a[$feature] ?? 0) - ($b[$feature] ?? 0), 2);
        }
        return sqrt($sum);
    }

    private function updateCentroids(array $data, array $assignments, array $features): array
    {
        $sums = array_fill(0, $this->k, array_fill_keys($features, 0));
        $counts = array_fill(0, $this->k, 0);

        foreach ($data as $idx => $point) {
            $cluster = $assignments[$idx];
            $counts[$cluster]++;
            foreach ($features as $feature) {
                $sums[$cluster][$feature] += $point[$feature];
            }
        }

        $centroids = [];
        for ($k = 0; $k < $this->k; $k++) {
            $centroids[$k] = [];
            foreach ($features as $feature) {
                $centroids[$k][$feature] = $counts[$k] > 0
                    ? $sums[$k][$feature] / $counts[$k]
                    : 0;
            }
        }

        return $centroids;
    }

    public function getClusterLabel(int $cluster): string
    {
        return match ($cluster) {
            0 => 'Protein Sedang',
            1 => 'Tinggi Energi Lengkap',
            2 => 'Tinggi Karbohidrat',
            3 => 'Seimbang',
            default => 'Cluster ' . ($cluster + 1),
        };
    }

    public function getClusterColor(int $cluster): string
    {
        return match ($cluster) {
            0 => '#2563EB',
            1 => '#F59E0B',
            2 => '#10B981',
            3 => '#8B5CF6',
            default => '#6B7280',
        };
    }

    public function getClusterDescription(int $cluster): string
    {
        return match ($cluster) {

            0 => 'Kelompok makanan dengan kandungan protein sedang dan nutrisi yang cukup baik untuk menu pendamping harian.',

            1 => 'Kelompok makanan dengan kandungan energi, protein, dan lemak tinggi yang cocok untuk mendukung kebutuhan gizi.',

            2 => 'Kelompok makanan dengan dominasi karbohidrat tinggi sebagai sumber energi utama dalam menu MBG.',

            3 => 'Kelompok makanan dengan komposisi nutrisi yang relatif seimbang untuk konsumsi harian.',

            default => 'Kelompok makanan dengan karakteristik nutrisi tertentu.',
        };
    }
}