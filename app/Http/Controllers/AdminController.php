<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disease;
use App\Models\Symptom;
use App\Services\AIService;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class AdminController extends Controller
{
    public function showUploadForm()
    {
        return view('admin.upload');
    }

    public function storeFromPdf(Request $request, AIService $aiService)
    {
        $request->validate(['pdf' => 'required|mimes:pdf|max:10000']);
        $file = $request->file('pdf');

        // DEBUG 1: Cek Upload ke AnythingLLM
// Di AdminController.php
        $uploaded = $aiService->uploadFileToAnythingLLM($file->path());
        if (!$uploaded) {
            // Ambil log terakhir dari Laravel
            return back()->with('error', 'Gagal di tahap AnythingLLM. Cek storage/logs/laravel.log untuk detailnya.');
        }

        // DEBUG 2: Cek Baca PDF
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->path());
            $text = $pdf->getText();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: Sistem tidak bisa membaca isi PDF ini.');
        }

        // DEBUG 3: Cek Ekstraksi AI
        $data = $aiService->extractMetadataFromText($text);
        if (!$data) {
            return back()->with('error', 'Gagal: AI (LMStudio) tidak merespon atau format JSON AI salah.');
        }

        // PROSES SIMPAN KE DATABASE
        \DB::transaction(function () use ($data) {
            $disease = \App\Models\Disease::updateOrCreate(
                ['name' => $data['disease_name']],
                [
                    'id' => (string) \Str::uuid(),
                    'severity_level' => $data['severity'],
                    'description' => $data['description'],
                    'pathogenesis_overview' => $data['pathogenesis_overview'],
                    'recovery_tips' => $data['recovery_tips'],
                    'red_flags' => $data['red_flags'],
                ]
            );

            // Di dalam loop symptoms pada AdminController
            foreach ($data['symptoms'] as $s) {
                // Normalisasi: Hapus spasi berlebih, jadikan Huruf Besar di Awal Kata
                $normalizedName = ucwords(strtolower(trim($s['name'])));

                $symptom = \App\Models\Symptom::firstOrCreate(
                    ['name' => $normalizedName],
                    [
                        'id' => (string) \Str::uuid(),
                        'category' => $s['category'] ?? 'Umum'
                    ]
                );

                $disease->symptoms()->syncWithoutDetaching([
                    $symptom->id => ['weight' => $s['weight'] ?? 0.5]
                ]);
            }
        });

        return back()->with('success', 'Berhasil! File sudah di AnythingLLM dan Database sudah update.');
    }
}