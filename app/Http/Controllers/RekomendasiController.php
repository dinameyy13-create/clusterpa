<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FoodDataService;

class RekomendasiController extends Controller
{
    public function rekomendasi(
        Request $request,
        FoodDataService $service
    ) {

        try {

            $kategori =
                $request->kategori;

            $data =
                $service->generateMBGMenu(
                    $kategori
                );

            return response()->json([

                'type' => 'mbg',

                'data' => $data

            ]);

        } catch (\Throwable $e) {

            return response()->json([

                'error' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile(),

            ], 500);
        }
    }
}