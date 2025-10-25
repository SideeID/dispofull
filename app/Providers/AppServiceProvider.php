<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Letter;
use App\Models\LetterType;
use App\Models\LetterDisposition;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share sidebar counts to all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $counts = $this->getSidebarCounts($user);
                $view->with('sidebarCounts', $counts);
            }
        });
    }

    /**
     * Get sidebar menu counts based on user role
     */
    private function getSidebarCounts($user): array
    {
        $counts = [];

        if ($user->role === 'rektorat') {
            $deptId = $user->department_id;
            $userId = $user->id;
            
            // Get Surat Tugas letter type ID
            $suratTugasTypeId = LetterType::where('code', 'ST')->value('id');

            // Count Surat Masuk (exclude Surat Tugas)
            $counts['surat_masuk'] = Letter::query()
                ->whereNull('archived_at')
                ->when($suratTugasTypeId, function($q) use ($suratTugasTypeId) {
                    $q->where('letter_type_id', '!=', $suratTugasTypeId);
                })
                ->where(function($q) use ($deptId, $userId){
                    $q->where(function($w) use ($deptId){
                        $w->where('direction', 'incoming')
                          ->when($deptId, function($qq) use ($deptId){
                              $qq->where(function($x) use ($deptId){
                                  $x->where('to_department_id',$deptId)
                                    ->orWhereNull('to_department_id');
                              });
                          });
                    })
                    ->orWhereHas('signatures', function($s) use ($userId){
                        $s->where('user_id',$userId);
                    });
                })
                ->count();

            // Count Surat Tugas (active, not archived)
            $counts['surat_tugas'] = Letter::query()
                ->where('letter_type_id', $suratTugasTypeId)
                ->where('direction', 'outgoing')
                ->whereNull('archived_at')
                ->count();

            // Count History Disposisi
            $counts['history_disposisi'] = LetterDisposition::query()
                ->whereHas('letter', function($q) use ($deptId){
                    $q->where('direction','incoming')
                      ->when($deptId, function($qq) use ($deptId){
                          $qq->where(function($x) use ($deptId){
                              $x->where('to_department_id',$deptId)
                                ->orWhereNull('to_department_id');
                          });
                      });
                })
                ->count();

            // Count Arsip Surat Tugas
            $counts['arsip_surat_tugas'] = Letter::query()
                ->where('letter_type_id', $suratTugasTypeId)
                ->where('direction', 'outgoing')
                ->where(function($q) {
                    $q->whereNotNull('archived_at')
                      ->orWhere('status', 'archived');
                })
                ->count();
        }

        return $counts;
    }
}
