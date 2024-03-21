<?php

namespace TFD\AIDA;

use Statamic\Events\AssetUploaded;
use Statamic\Providers\AddonServiceProvider;
use TFD\AIDA\Actions\GenerateAltTextAction;
use TFD\AIDA\Generator\Generator;
use TFD\AIDA\Listeners\GenerateAltTextOnUpload;

class ServiceProvider extends AddonServiceProvider
{
    protected $actions = [
        GenerateAltTextAction::class,
    ];

    protected $listen = [
        AssetUploaded::class => [
            GenerateAltTextOnUpload::class,
        ],
    ];

    public function bootAddon()
    {
        $this
            ->bootAddonConfig()
            ->bindGenerator();
    }

    protected function bootAddonConfig(): self
    {
        $this->mergeConfigFrom(__DIR__.'/../config/aida.php', 'statamic.aida');

        $this->publishes([
            __DIR__.'/../config/aida.php' => config_path('statamic/aida.php'),
        ], 'aida-config');

        return $this;
    }

    private function bindGenerator(): self
    {
        $this->app->bind(Generator::class, config('statamic.aida.generator'));

        return $this;
    }
}
