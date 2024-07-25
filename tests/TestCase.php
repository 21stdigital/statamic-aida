<?php

namespace TFD\AIDA\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Statamic\Assets\Asset;
use Statamic\Assets\AssetContainer;
use Statamic\Console\Commands\GlideClear;
use Statamic\Extend\Manifest;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Site;
use Statamic\Facades\Stache;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;
use TFD\AIDA\Generator\DummyGenerator;
use TFD\AIDA\ServiceProvider;
use Wilderborn\Partyline\ServiceProvider as PartylineServiceProvider;

class TestCase extends BaseTestCase
{
    /** @var AssetContainer */
    public $assetContainer;

    protected function setUp(): void
    {
        parent::setUp();

        // Clean up from old tests
        File::deleteDirectory($this->getTempDirectory());

        $this->setUpTempTestFiles();

        $this->artisan(GlideClear::class);

        config(['filesystems.disks.assets' => [
            'driver' => 'local',
            'root' => $this->getTempDirectory('assets'),
            'url' => '/test',
        ]]);

        $this->assetContainer = (new AssetContainer);
        $this->assetContainer->handle('test_container');
        $this->assetContainer->disk('assets');
        $this->assetContainer->save();
    }

    protected function tearDown(): void
    {
        File::deleteDirectories($this->getTempDirectory());
        Stache::clear();

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            PartylineServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(Manifest::class)->manifest = [
            'tfd/statamic-aida' => [
                'id' => 'tfd/statamic-aida',
                'namespace' => 'TFD\\AIDA',
            ],
        ];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configs = [
            'assets', 'cp', 'forms', 'routes', 'static_caching', 'stache', 'system', 'users',
        ];

        foreach ($configs as $config) {
            $app['config']->set("statamic.$config", require (__DIR__."/../vendor/statamic/cms/config/{$config}.php"));
        }

        // Setting the user repository to the default flat file system.
        $app['config']->set('statamic.users.repository', 'file');

        // Assume the pro edition within tests
        $app['config']->set('statamic.editions.pro', true);

        // Define config settings for all of the tests
        $app['config']->set('statamic.aida', require (__DIR__.'/../config/aida.php'));

        // Use dummy generator
        $app['config']->set('statamic.aida.generator', DummyGenerator::class);

        $app['config']->set('statamic.stache.stores.collections.directory', $this->getTempDirectory('/content/collections'));
        $app['config']->set('statamic.stache.stores.entries.directory', $this->getTempDirectory('/content/collections')); // TODO: Check path, should it be '/content/entries'?
        $app['config']->set('statamic.stache.stores.asset-containers.directory', $this->getTempDirectory('/content/assets'));

        Statamic::booted(function () {
            Blueprint::setDirectory($this->getTempDirectory('resources/blueprints'));
        });
    }

    protected function getTempDirectory(string $suffix = ''): string
    {
        return __DIR__.'/TestSupport/tmp'.($suffix == '' ? '' : '/'.$suffix);
    }

    protected function setUpTempTestFiles()
    {
        $this->initializeDirectory(__DIR__.'/TestSupport/tmp');
        $this->initializeDirectory($this->getTestFilesDirectory());
        File::copyDirectory(__DIR__.'/TestSupport/TestFiles', $this->getTestFilesDirectory());
    }

    protected function initializeDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }

        File::makeDirectory($directory, 0755, true);
    }

    public function getTestFilesDirectory($suffix = ''): string
    {
        return $this->getTempDirectory().'/testfiles'.($suffix == '' ? '' : '/'.$suffix);
    }

    public function getTestJpg(): string
    {
        return $this->getTestFilesDirectory('test.jpg');
    }

    public function getTestPng(): string
    {
        return $this->getTestFilesDirectory('test.png');
    }

    public function getTestGif(): string
    {
        return $this->getTestFilesDirectory('test.gif');
    }

    public function getTestWebP(): string
    {
        return $this->getTestFilesDirectory('test.webp');
    }

    public function getTestSvg(): string
    {
        return $this->getTestFilesDirectory('test.svg');
    }

    public function getTestZip(): string
    {
        return $this->getTestFilesDirectory('test.zip');
    }

    public function getTestPdf(): string
    {
        return $this->getTestFilesDirectory('test.pdf');
    }

    public function uploadTestImageToTestContainer(?string $testImagePath = null, ?string $filename = 'test.jpg'): Asset
    {
        if ($testImagePath === null) {
            $testImagePath = test()->getTestJpg();
        }

        $file = new UploadedFile($testImagePath, $filename);
        $path = ltrim('/'.$file->getClientOriginalName(), '/');

        return $this->assetContainer->makeAsset($path)->upload($file);
    }

    public function useMultiSite(): void
    {
        $multiSites = [
            'default' => [
                'name' => config('app.name'),
                'locale' => 'en_US',
                'url' => '/',
            ],
            'german' => [
                'name' => 'German',
                'locale' => 'de_DE',
                'url' => '/de/',
            ],
            'french' => [
                'name' => 'French',
                'locale' => 'fr',
                'url' => '/fr/',
            ],
        ];

        Site::setSites($multiSites);
    }
}
