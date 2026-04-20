<?php

namespace App\Http\Middleware;

use App\Filament\Pages\DashboardPage;
use App\Models\ChartOfAccount;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OnBoarding {
    public function handle(Request $request, Closure $next): Response {
        if (Auth::check()) {
            if (ChartOfAccount::count() <= 1) {
                if (
                    !$request->routeIs('filament.admin.pages.on-boarding') && 
                    !$request->routeIs('livewire.*') && 
                    !$request->routeIs('filament.admin.auth.logout')
                ) {
                    return redirect()->route('filament.admin.pages.on-boarding');
                }
            } else {
                if ($request->routeIs('filament.admin.pages.on-boarding')) {
                    return redirect(DashboardPage::getUrl());
                }
            }
        }

        return $next($request);
    }
}
