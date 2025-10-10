<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                $safe = function(callable $cb) {
                    try { return (int) $cb(); } catch (\Throwable $e) { return 0; }
                };

                $today = Carbon::today();
                $yesterday = Carbon::yesterday();

                $incomingToday    = $safe(fn()=> DB::table('surat_masuk')->whereDate('created_at', $today)->count());
                $incomingYesterday= $safe(fn()=> DB::table('surat_masuk')->whereDate('created_at', $yesterday)->count());
                $outgoingDraft    = $safe(fn()=> DB::table('surat_keluar')->where('status','draft')->count());
                $disposisiPending = $safe(fn()=> DB::table('disposisi')->where('status','pending')->count());
                $needSignature    = $safe(fn()=> DB::table('signatures')->where('status','waiting')->count());

                $incomingDelta = '0%';
                if ($incomingYesterday > 0) {
                    $diff = $incomingToday - $incomingYesterday;
                    $pct  = $incomingYesterday ? (($diff / $incomingYesterday) * 100) : 0;
                    $incomingDelta = ($diff >= 0 ? '+' : '').number_format($pct,0).'%' ;
                } elseif ($incomingToday > 0) {
                    $incomingDelta = '+100%';
                }

                $cards = [
                    [
                        'label' => 'Surat Masuk (Hari ini)',
                        'value' => $incomingToday,
                        'icon'  => 'inbox',
                        'color' => 'from-amber-500 to-orange-500',
                        'delta' => $incomingDelta,
                    ],
                    [
                        'label' => 'Surat Keluar (Draft)',
                        'value' => $outgoingDraft,
                        'icon'  => 'edit',
                        'color' => 'from-blue-500 to-indigo-500',
                        'delta' => $outgoingDraft > 0 ? $outgoingDraft.' draft' : '0',
                    ],
                    [
                        'label' => 'Disposisi Pending',
                        'value' => $disposisiPending,
                        'icon'  => 'git-branch',
                        'color' => 'from-rose-500 to-pink-500',
                        'delta' => $disposisiPending > 0 ? $disposisiPending.' urgent' : '0',
                    ],
                    [
                        'label' => 'Butuh Tanda Tangan',
                        'value' => $needSignature,
                        'icon'  => 'pen-tool',
                        'color' => 'from-teal-500 to-emerald-500',
                        'delta' => $needSignature > 0 ? $needSignature.' prioritas' : '0',
                    ],
                ];

                return view('pages.admin.dashboard', compact('cards'));
            }  elseif ($user->role === 'rektorat') {
                return view('pages.rektorat.dashboard');
            } else if ($user->role === 'unit_kerja') {
                return view('pages.unit_kerja.dashboard');
            } else {
                return redirect()->route('login')->with('error', 'Unauthorized access.');
            }
        }
    }
}
