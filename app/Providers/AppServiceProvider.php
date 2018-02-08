<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Config;
use Cloudinary;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Cloudinary::config(array(
            'cloud_name' => Config::get('cloudinary.cloudinary.cloud_name'),
            'api_key' => Config::get('cloudinary.cloudinary.api_key'),
            'api_secret' => Config::get('cloudinary.cloudinary.api_secret')
        ));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
