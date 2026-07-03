<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
{
    // Tambahkan ini agar Carbon dan PHP selalu sinkron ke Jakarta
    config(['app.timezone' => 'Asia/Jakarta']);
    date_default_timezone_set('Asia/Jakarta');
    \Carbon\Carbon::setLocale('id');
}
}
