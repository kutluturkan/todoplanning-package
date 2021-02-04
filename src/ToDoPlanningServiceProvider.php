<?php

namespace Kutluturkan\ToDoPlanning;

use Illuminate\Support\ServiceProvider;
use Kutluturkan\ToDoPlanning\Console\Commands\ToDoListRegister;

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
            __DIR__ . '/views' => resource_path('views/vendor/todoplanning'),
        ]);

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->commands([
                ToDoListRegister::class,
            ]);
        }
    }

    public function register()
    {
        // no-command
    }
}
