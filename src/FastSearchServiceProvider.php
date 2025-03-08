<?php

namespace SumeetGhimire\FastSearch;

use Illuminate\Support\ServiceProvider;

class FastSearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FastSearch::class, function () {
            return new FastSearch();
        });
    }

    public function boot()
    {
        // Publish config files or perform other boot actions if needed
    }
}
