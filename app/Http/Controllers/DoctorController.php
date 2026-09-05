<?php

namespace App\Http\Controllers;

use App\Models\OnlineDoctorProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /**
     * Tampilkan direktori dokter spesialis paru dan respirasi mitra telemedika ISHAP.
     */
    public function index(Request $request): View
    {
        $query = OnlineDoctorProfile::query();

        if ($platform = $request->input('platform')) {
            $query->where('platform', 'like', "%{$platform}%");
        }

        $doctors = $query->get();

        return view('doctors.index', compact('doctors'));
    }
}
