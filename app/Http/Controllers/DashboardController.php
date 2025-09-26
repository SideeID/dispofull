<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                $dataFeed = new DataFeed();
                return view('pages.admin.dashboard', compact('dataFeed'));
            }  elseif ($user->role === 'rektorat') {
                return view('pages.rektorat.dashboard');
            } else if ($user->role === 'unit_kerja') {
                return view('pages.unit_kerja.dashboard');
            } else {
                return redirect()->route('login')->with('error', 'Unauthorized access.');
            }
        }
    }

    public function analytics()
    {
        return view('pages/dashboard/analytics');
    }

    public function fintech()
    {
        return view('pages/dashboard/fintech');
    }
}
