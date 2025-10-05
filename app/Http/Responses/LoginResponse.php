<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        $user = Auth::user();

        // Gunakan fallback aman jika nama route khusus belum terdaftar
        $routeFor = function(string $preferred, string $fallback) {
            return \Illuminate\Support\Facades\Route::has($preferred)
                ? $preferred
                : (\Illuminate\Support\Facades\Route::has($fallback) ? $fallback : 'login');
        };

        switch ($user->role) {
            case 'admin':
                return redirect()->route($routeFor('dashboard.admin', 'dashboard'));
            case 'rektorat':
                return redirect()->route($routeFor('dashboard.rektorat', 'dashboard'));
            case 'unit_kerja':
                return redirect()->route($routeFor('dashboard.unit_kerja', 'dashboard'));
            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    }
}
