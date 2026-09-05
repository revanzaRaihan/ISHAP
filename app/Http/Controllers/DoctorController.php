<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /**
     * Tampilkan direktori layanan konsultasi online dokter spesialis via deep link Halodoc.
     */
    public function index(Request $request): View
    {
        $categories = [
            [
                'id' => 'paru',
                'title' => 'Dokter Spesialis Paru & Pernapasan',
                'specialty' => 'Pulmonologi & Kedokteran Respirasi',
                'platform' => 'Halodoc',
                'url' => 'https://www.halodoc.com/tanya-dokter/kategori/kesehatan-paru',
                'initial' => 'P',
                'description' => 'Konsultasi batuk persisten, bronkitis, pneumonia, asma, dan saluran napas bawah.',
            ],
            [
                'id' => 'tht',
                'title' => 'Dokter Spesialis THT',
                'specialty' => 'Telinga, Hidung & Tenggorokan',
                'platform' => 'Halodoc',
                'url' => 'https://www.halodoc.com/tanya-dokter/kategori/spesialis-tht',
                'initial' => 'T',
                'description' => 'Konsultasi radang tenggorokan, faringitis, amandel, sinusitis, dan saluran napas atas.',
            ],
            [
                'id' => 'anak',
                'title' => 'Dokter Spesialis Anak',
                'specialty' => 'Pediatri & Kesehatan Anak',
                'platform' => 'Halodoc',
                'url' => 'https://www.halodoc.com/tanya-dokter/kategori/spesialis-anak',
                'initial' => 'A',
                'description' => 'Konsultasi batuk pilek, demam, dan infeksi pernapasan akut khusus bayi dan balita.',
            ],
        ];

        return view('doctors.index', compact('categories'));
    }
}

