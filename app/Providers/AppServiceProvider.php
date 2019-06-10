<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        $relative_path = 'images/schnappis/';
        $pic_dir       = public_path($relative_path);
        $files         = File::files($pic_dir);

        $schnappis = [];
        foreach ($files as $file) {
            $schnappis[] = $relative_path . $file->getBasename();
        }
        shuffle($schnappis);
        View::share('schnappis', $schnappis);

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
