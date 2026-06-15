<?php

namespace App\Http\Controllers;

use App\Services\FoodDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIRecipeController extends Controller
{
    protected $foodService;

    public function __construct(FoodDataService $foodService)
    {
        $this->foodService = $foodService;
    }

    /**
     * Halaman awal
     */
    public function index()
    {
        $foods = collect($this->foodService->getClusteredFoods())
            ->where('kategori_data', 'bahan')
            ->sortBy('nama')
            ->values();

        // Variabel hasil diinisialisasi null agar tidak error di blade
        $hasil = null;

        return view('ai-recipe', compact('foods', 'hasil'));
    }

    /**
     * Generate rekomendasi AI
     */
    public function generate(Request $request)
    {
        $request->validate([
            'bahan' => 'required|array|min:1',
        ], [
            'bahan.required' => 'Silakan pilih minimal satu bahan.',
        ]);

        $allFoods = collect($this->foodService->getClusteredFoods());

        $foods = $allFoods
            ->where('kategori_data', 'bahan')
            ->sortBy('nama')
            ->values();

        $bahanUser = implode(', ', $request->bahan);

        $prompt = "
        Kamu adalah ahli kuliner Indonesia. 
        Saya punya bahan: {$bahanUser}. 
        Berikan 3 rekomendasi menu masakan Indonesia. 

        Format jawaban HARUS persis seperti ini untuk setiap menu:

        1. Nama Menu
        - Bahan tambahan: [Bahan utama]
        - Analisis Nutrisi: [Perkiraan kalori, protein, serat]

        Jawab dalam Bahasa Indonesia dan gunakan baris baru untuk setiap poin.
        ";

        try {
            $response = Http::timeout(60)
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . env('GEMINI_API_KEY'),
                    [
                        "contents" => [
                            ["parts" => [["text" => $prompt]]]
                        ]
                    ]
                );

            // Cek apakah API sukses
            if ($response->successful()) {
                $hasil = $response->json('candidates.0.content.parts.0.text') ?? 'Tidak ada jawaban dari AI.';
            } else {
                $hasil = 'Gagal terhubung ke AI. Silakan coba lagi.';
            }

            return view('ai-recipe', compact('foods', 'hasil'));

        } catch (\Exception $e) {
                // Ubah ini untuk melihat error aslinya di layar
                dd($e->getMessage()); 
            }
    }
}