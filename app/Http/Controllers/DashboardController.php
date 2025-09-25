<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;

class DashboardController extends Controller
{
    public function sessions()
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
            preg_match_all('/login_admin_[^;]*;i:\d+/', $content, $admin_matches);
            if (!empty($admin_matches[0])) {
                $total_admins += count($admin_matches[0]);
                $admins_raw = array_merge($admins_raw, $admin_matches[0]);
            }

            // Contar sesiones SAT
            preg_match_all('/login_web_[^;]*;i:\d+/', $content, $sat_matches);
            if (!empty($sat_matches[0])) {
                $total_sat += count($sat_matches[0]);
                $sat_raw = array_merge($sat_raw, $sat_matches[0]);
            }
        }

        $total_sessions = $total_admins + $total_sat;

        return view('sessions', compact(
            'total_sessions', 'total_admins', 'total_sat'
        ));
    }
}
