<?php

namespace YukataRm\Laravel\Starter;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use YukataRm\Laravel\Starter\Commands\StarterCommand;
use YukataRm\Laravel\Starter\Commands\PublishStubsCommand;

use Illuminate\Support\Facades\Blade;

/**
 * Starter Service Provider
 *
 * @package YukataRm\Laravel\Starter
 */
class ServiceProvider extends BaseServiceProvider
{
    /*----------------------------------------*
     * Boot
     *----------------------------------------*/

    /**
     * boot
     *
     * @return void
     */
    public function boot(): void
    {
        $this->bootCommands();
        $this->bootViews();
    }

    /**
     * boot commands
     *
     * @return void
     */
    protected function bootCommands(): void
    {
        if (!$this->app->runningInConsole()) return;

        $this->commands([
            StarterCommand::class,
            PublishStubsCommand::class,
        ]);
    }

    /**
     * boot views
     *
     * @return void
     */
    protected function bootViews(): void
    {
        $this->loadViewsFrom(__DIR__ . "/../resources/views", "yr-starter");

        Blade::componentNamespace("YukataRm\\Laravel\\Starter\\Components", "yr-starter");
    }

    /*----------------------------------------*
     * Register
     *----------------------------------------*/

    /**
     * register
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfigs();
    }

    /**
     * register configs
     *
     * @return void
     */
    protected function registerConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . "/../configs/yr-starter.php", "yr-starter");
    }
}
