<?php

namespace App\Providers;

use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Response;
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
        if (config('app.debug')) { 
            // \DB::enableQueryLog();
        }

        Response::macro('success', function (mixed $data = [], string $message = 'Success', $code = 200): mixed {
            return ResponseHelper::responseJson('success', $data, $message, $code);
        });

        Response::macro('error', function (mixed $data = [], string $message = 'Error', $code = 200): mixed {
            return ResponseHelper::responseJson('error', $data, $message, $code);
        });

        app()->terminating(function () {
            // \Log::debug(array_reduce(\DB::getRawQueryLog(), fn ($c, $i) => $c .= $i['raw_query'] . PHP_EOL, ''));
            // \DB::disableQueryLog();
        });
    }
}
