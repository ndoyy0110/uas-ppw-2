<?php

namespace App\Http\Controllers;
use App\Models\Pegawai;
use App\Models\Pekerjaan;
class MainController extends Controller
{
    public function index() {
        $maleCount = Pegawai::where('gender', 'male')->count();
        $femaleCount = Pegawai::where('gender', 'female')->count();

        $topJobs = Pekerjaan::withCount('pegawai')
            ->orderBy('pegawai_count', 'desc')
            ->limit(5)
            ->get();

        $jobLabels = $topJobs->pluck('nama')->toArray(); // Perhatikan: di seeder kolomnya 'nama', bukan 'nama_pekerjaan'
        $jobCounts = $topJobs->pluck('pegawai_count')->toArray();

        return view('index', compact('maleCount', 'femaleCount', 'jobLabels', 'jobCounts'));
    }
}
