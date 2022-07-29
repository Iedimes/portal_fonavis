<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware('auth');
    }*/

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function verification($key)
    {

        //return $key;
        $project = Project::where('certificate_pin', $key)
            //->select('name', 'last_name', 'government_id', 'farm', 'account', 'amount', 'state_id', 'city_id', 'created_at')
            ->first();

        //return $task;
        return view('verification', compact('project'));
    }
}
