<?php

namespace App\Console\Commands;

use App\Services\SupabaseService;
use Illuminate\Console\Command;

class SupabaseSyncCommand extends Command
{
    protected $signature = 'supabase:sync';
    protected $description = 'Sinkronisasi master data (gejala, penyakit, bobot, dokter) dari Supabase ke database lokal';

    public function handle(SupabaseService $supabase): int
    {
        $this->info('Memulai sinkronisasi master data dari Supabase...');

        $stats = $supabase->syncRemoteToLocal();

        $this->info('✅ Sinkronisasi Supabase Selesai:');
        $this->line("   - Gejala (Symptoms): {$stats['symptoms']}");
        $this->line("   - Penyakit (Diseases): {$stats['diseases']}");
        $this->line("   - Pemetaan Bobot (Maps): {$stats['maps']}");
        $this->line("   - Dokter Online (Doctors): {$stats['doctors']}");

        return Command::SUCCESS;
    }
}
