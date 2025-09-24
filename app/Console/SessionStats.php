<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class SessionStats extends Command
{
    protected $signature = 'sessions:stats';
    protected $description = 'Muestra estadísticas de sesiones';

    public function handle()
    {
        $path = storage_path('framework/sessions');
        $files = File::files($path);

        $total = count($files);
        $admins_raw = [];
        $sat_raw = [];

        foreach ($files as $file) {
            $content = File::get($file);
            if (preg_match('/login_admin_[^;]*;i:(\d+)/', $content, $m)) {
                $admins_raw[] = $m[1];
            }
            if (preg_match('/login_web_[^;]*;i:(\d+)/', $content, $m)) {
                $sat_raw[] = $m[1];
            }
        }

        $admins_unique = array_unique($admins_raw);
        $sat_unique = array_unique($sat_raw);

        $adminCountUnique = count($admins_unique);
        $satCountUnique = count($sat_unique);
        $guestCount = $total - count($admins_raw) - count($sat_raw);

        $this->info("Total sesiones activas: $total");
        $this->info("Sesiones admin: " . count($admins_raw));
        $this->info("Usuarios admin únicos: $adminCountUnique");
        $this->info("Sesiones SAT: " . count($sat_raw));
        $this->info("Usuarios SAT únicos: $satCountUnique");
        $this->info("Sesiones de invitados: $guestCount");
    }
}
