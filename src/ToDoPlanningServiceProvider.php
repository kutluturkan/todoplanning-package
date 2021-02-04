<?php

namespace Kutluturkan\ToDoPlanning;

use Illuminate\Support\ServiceProvider;

class ToDoPlanningServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'todoplanning');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/config/todoplanning.php', 'todoplanning');
        $this->publishes([
            __DIR__ . '/config/todoplanning.php' => config_path('todoplanning.php'),
        ]);
    }

    public function register()
    {
        # code...
    }
}
