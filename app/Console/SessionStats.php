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

        $admins_raw = [];
        $sat_raw = [];
        $total_admins = 0;
        $total_sat = 0;

        foreach ($files as $file) {
            $content = File::get($file);

            // Contar sesiones admin
            if (preg_match_all('/login_admin_[^;]*;i:\d+/', $content, $matches_admin)) {
                $total_admins += count($matches_admin[0]);
                $admins_raw = array_merge($admins_raw, $matches_admin[0]);
            }

            // Contar sesiones SAT
            if (preg_match_all('/login_web_[^;]*;i:\d+/', $content, $matches_sat)) {
                $total_sat += count($matches_sat[0]);
                $sat_raw = array_merge($sat_raw, $matches_sat[0]);
            }
        }

        $total_sessions = $total_admins + $total_sat;

        $this->info("Total de sesiones admin: $total_admins");
        $this->info("Total de sesiones SAT: $total_sat");
        $this->info("Total de sesiones (admin + SAT): $total_sessions");
    }
}
