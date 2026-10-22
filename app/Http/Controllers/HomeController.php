<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'appName' => 'AI Client Intake',
            'workflowStatus' => 'Alur review siap digunakan',
            'nextStep' => 'Mulai dari data intake, lalu lanjutkan ke workspace reviewer.',
        ]);
    }
}
