<?php

namespace App\Console\Commands;

use App\Services\SupabaseService;
use Illuminate\Console\Command;

class SupabaseTestCommand extends Command
{
    protected $signature = 'supabase:test';
    protected $description = 'Uji koneksi ke Supabase REST API dan cek latensi';

    public function handle(SupabaseService $supabase): int
    {
        $this->info('Menguji koneksi ke Supabase...');
        $result = $supabase->testConnection();

        if (($result['status'] ?? '') === 'connected') {
            $this->info('✅ ' . $result['message']);
            $this->line('   URL: ' . $result['url']);
            $this->line('   Latensi: ' . $result['latency_ms'] . ' ms');
            return Command::SUCCESS;
        }

        $this->error('❌ Gagal terhubung ke Supabase: ' . ($result['message'] ?? 'Unknown error'));
        return Command::FAILURE;
    }
}
