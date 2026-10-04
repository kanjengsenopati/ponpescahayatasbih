<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ApplicationSetting;

class LogoutAllWali extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wali:logout-all {--keep-open : Jangan mengunci akses login sistem, hanya logout saat ini}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Paksa auto-logout seluruh wali santri (cabut semua sesi web dan token mobile)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=========================================================");
        $this->info("   MEMULAI PROSES AUTO-LOGOUT SELURUH WALI SANTRI        ");
        $this->info("=========================================================");

        // 1. Cabut seluruh token mobile app (Laravel Passport)
        $revokedTokens = 0;
        if (Schema::hasTable('oauth_access_tokens')) {
            $revokedTokens = DB::table('oauth_access_tokens')
                ->where('revoked', 0)
                ->update(['revoked' => 1]);

            if (Schema::hasTable('oauth_refresh_tokens')) {
                DB::table('oauth_refresh_tokens')
                    ->where('revoked', 0)
                    ->update(['revoked' => 1]);
            }
            $this->info("✓ Berhasil mencabut {$revokedTokens} token aktif aplikasi mobile.");
        }

        // 2. Bersihkan file sesi web
        $sessionPath = storage_path('framework/sessions');
        $clearedSessions = 0;
        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    if (@unlink($file)) {
                        $clearedSessions++;
                    }
                }
            }
            $this->info("✓ Berhasil menghapus {$clearedSessions} file sesi web browser.");
        }

        // 3. Bersihkan tabel database session jika menggunakan database session driver
        if (Schema::hasTable('sessions')) {
            $dbSessions = DB::table('sessions')->count();
            DB::table('sessions')->truncate();
            $this->info("✓ Berhasil menghapus {$dbSessions} sesi database.");
        }

        // 4. Kunci akses login jika tidak memakai flag --keep-open
        if (!$this->option('keep-open')) {
            $setting = ApplicationSetting::first();
            if ($setting) {
                $setting->is_login_locked = true;
                $setting->save();
                $this->info("✓ Status Kunci Login Sistem: DIKUNCI (Wali santri tidak dapat login kembali).");
            }
        }

        $this->newLine();
        $this->info("SELESAI! Seluruh wali santri telah berhasil di-logout otomatis.");
        return 0;
    }
}
