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

        foreach ($files as $file) {
    $content = File::get($file);

    // Todas las coincidencias admin
    preg_match_all('/login_admin_[^;]*;i:[0-9]+/', $content, $matches);
    $admins_raw = array_merge($admins_raw, $matches[0]);

    // Todas las coincidencias SAT
    preg_match_all('/login_web_[^;]*;i:[0-9]+/', $content, $matches);
    $sat_raw = array_merge($sat_raw, $matches[0]);
    }


        $admins_unique = array_unique($admins_raw);
        $sat_unique = array_unique($sat_raw);

        $total = count($files);
        $guest_count = $total - count($admins_raw) - count($sat_raw);

        return view('sessions', compact(
            'total', 'admins_raw', 'admins_unique', 'sat_raw', 'sat_unique', 'guest_count'
        ));
    }
}
