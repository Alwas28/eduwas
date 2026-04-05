<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
        // @canaccess('lihat.fakultas') ... @endcanaccess
        Blade::if('canaccess', function (string ...$accesses): bool {
            $user = auth()->user();
            if (!$user) return false;
            $userAccesses = $user->allAccesses();
            foreach ($accesses as $access) {
                if ($userAccesses->contains($access)) return true;
            }
            return false;
        });
    }
}
