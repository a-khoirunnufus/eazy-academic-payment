<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;
use App\Extensions\SessionGuardExtended;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

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
        Auth::extend(
            'sessionExtended',
            function ($app) {
                $provider = new EloquentUserProvider($app['hash'], config('auth.providers.users.model'));
                return new SessionGuardExtended('sessionExtended', $provider, app()->make('session.store'), request());
            }
        );

        if ( env('APP_SERVER') == 'btp' ) {
            \URL::forceRootUrl(env('APP_URL'));
            \URL::forceScheme('https');
        }
        Paginator::useBootstrap();
    }
}
