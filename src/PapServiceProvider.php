<?php

namespace RazeSoldier\Seat3VPap;

use RazeSoldier\Seat3VPap\Jobs\UpdateCorpPap;
use Seat\Services\AbstractSeatPlugin;

class PapServiceProvider extends AbstractSeatPlugin
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
        $this->mergeConfigFrom(__DIR__ . '/Config/pap.sidebar.php', 'package.sidebar');
    }

    private function addCommand()
    {
        $this->commands([UpdateCorpPap::class]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'PAP';
    }

    /**
     * @inheritDoc
     */
    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/RazeSoldier/seat-3vpap';
    }

    /**
     * @inheritDoc
     */
    public function getPackagistPackageName(): string
    {
        return 'seat-3vpap';
    }

    /**
     * @inheritDoc
     */
    public function getPackagistVendorName(): string
    {
        return 'razesoldier';
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '0.2.1';
    }
}
