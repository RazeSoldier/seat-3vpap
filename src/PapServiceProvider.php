<?php

namespace RazeSoldier\Seat3VPap;

use Illuminate\Support\ServiceProvider;
use RazeSoldier\Seat3VPap\Jobs\UpdateCorpPap;

class PapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addRoutes();
        $this->addView();
        $this->addTranslations();
        $this->addConfig();
        $this->addCommand();
    }

    /**
     * Include the routes.
     */
    private function addRoutes()
    {
        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/Http/routes.php';
        }
    }

    private function addView()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'pap');
    }

    private function addTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/i18n', 'pap');
    }

    private function addConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/pap.permissions.php', 'web.permissions');
    }

    private function addCommand()
    {
        $this->commands([UpdateCorpPap::class]);
    }
}
