<?php

namespace MaduLinux\RepositoryPattern;

use Illuminate\Support\ServiceProvider;
use MaduLinux\RepositoryPattern\Console\Commands\MakeRepositoryCommand;
use MaduLinux\RepositoryPattern\Console\Commands\MakeCriteriaCommand;
use MaduLinux\RepositoryPattern\Console\Commands\MakeServiceCommand;
use MaduLinux\RepositoryPattern\Console\Commands\MakePatternCommand;

class RepositoryPatternServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
                MakeCriteriaCommand::class,
                MakeServiceCommand::class,
                MakePatternCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/stubs' => base_path('stubs/repository-pattern'),
            ], 'repository-pattern-stubs');
        }
    }
}
