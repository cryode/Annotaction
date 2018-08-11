<?php

declare(strict_types=1);

namespace Cryode\Annotaction;

use Cryode\Annotaction\Console\ActionMake;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;

class AnnotactionServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/annotaction.php' => config_path('annotaction.php'),
        ], 'config');

        if ( ! $this->app->routesAreCached()) {
            /** @var LoadActionRoutes $loader */
            $loader = $this->app->make(LoadActionRoutes::class);
            $loader();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/annotaction.php',
            'annotaction'
        );

        $this->app->singleton(LoadActionRoutes::class, function (Application $app) {
            return new LoadActionRoutes(
                $app->make(Filesystem::class),
                $app->make(Router::class),
                (string) config('annotaction.action_dir')
            );
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionMake::class,
            ]);
        }
    }

    public function provides(): array
    {
        return [
            LoadActionRoutes::class,
        ];
    }
}
