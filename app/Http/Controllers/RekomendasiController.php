<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FoodDataService; // ✅ WAJIB

class RekomendasiController extends Controller
{
    public function rekomendasi(Request $request, FoodDataService $service)
    {
        $tujuan = $request->tujuan;
        $userKategori = $request->kategori;

        // validasi kategori
        $validKategori = ['anak', 'remaja', 'ibu'];
        if (!in_array($userKategori, $validKategori)) {
            return response()->json([
                'error' => 'Kategori tidak valid'
            ], 400);
        }

        // validasi stunting
        if ($tujuan === 'stunting' && $userKategori !== 'anak') {
            return response()->json([
                'error' => 'Menu stunting hanya untuk anak-anak'
            ], 400);
        }

        // MBG
        if ($tujuan === 'mbg') {
            return response()->json([
                'type' => 'mbg',
                'kategori' => $userKategori,
                'data' => $service->generateMBGMenu($userKategori) // ✅ kirim userKategori
            ]);
        }

        // STUNTING
        return response()->json([
            'type' => 'stunting',
            'data' => $service->generateStuntingMenu()
        ]);
    }
}