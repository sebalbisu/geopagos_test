<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        // display sql queries on logs
        // if (! App::isProduction()) {
        //     DB::listen(function (QueryExecuted $query) {
        //         Log::debug(
        //             $query->sql."\n".
        //             json_encode($query->bindings)."\n".
        //             $query->time
        //         );
        //     });
        // }
    }
}
