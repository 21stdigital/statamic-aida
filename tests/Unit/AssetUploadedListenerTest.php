<?php

use Illuminate\Support\Facades\Queue;
use Statamic\Facades\Asset as FacadesAsset;
use TFD\AIDA\Generator\DummyGenerator;
use TFD\AIDA\Jobs\GenerateAltTextJob;

it('does not create a job by default when an asset is uploaded', function () {
    Queue::fake();

    $this->asset = test()->uploadTestImageToTestContainer();

    $pushedJobs = Queue::pushed(GenerateAltTextJob::class);

    expect($pushedJobs->count())->toBe(0);
    Queue::assertPushed(GenerateAltTextJob::class, 0);
});

it('creates a job when an asset is uploaded with configuration', function () {
    Queue::fake();

    config()->set('statamic.aida.generate_on_upload', true);

    $this->asset = test()->uploadTestImageToTestContainer();

    $pushedJobs = Queue::pushed(GenerateAltTextJob::class);

    expect($pushedJobs->count())->toBe(1);
    Queue::assertPushed(GenerateAltTextJob::class, 1);
});

it('generates an alt text for every site', function () {
    $generator = new DummyGenerator;
    Queue::fake();

    config()->set('statamic.aida.generate_on_upload', true);
    test()->useMultiSite();

    $this->asset = test()->uploadTestImageToTestContainer();
    $pushedJobs = Queue::pushed(GenerateAltTextJob::class);

    // Actually execute the queued import job
    $job = $pushedJobs->first();
    $job->handle($generator);

    $altTextEn = $generator->generate($this->asset, 'en');
    $altTextDe = $generator->generate($this->asset, 'de');
    $altTextFr = $generator->generate($this->asset, 'fr');

    // After exeucting the import job, the alt text should be generated for the image.
    $assetWithAlt = FacadesAsset::findById($this->asset->id());

    expect($assetWithAlt->alt_en)->toBeString($altTextEn);
    expect($assetWithAlt->alt_de)->toBeString($altTextDe);
    expect($assetWithAlt->alt_fr)->toBeString($altTextFr);
});

it('creates only a single job for multiple sites', function () {
    Queue::fake();

    config()->set('statamic.aida.generate_on_upload', true);
    test()->useMultiSite();

    $this->asset = test()->uploadTestImageToTestContainer();

    $pushedJobs = Queue::pushed(GenerateAltTextJob::class);

    expect($pushedJobs->count())->toBe(1);
    Queue::pushedJobs(GenerateAltTextJob::class, 1);
});

it('does dispatch jobs for jpg, png, gif and webp images', function () {
    Queue::fake();
    config()->set('statamic.aida.generate_on_upload', true);

    // Upload valid image types
    test()->uploadTestImageToTestContainer(test()->getTestJpg(), 'test.jpg');
    test()->uploadTestImageToTestContainer(test()->getTestPng(), 'test.png');
    test()->uploadTestImageToTestContainer(test()->getTestGif(), 'test.gif');
    test()->uploadTestImageToTestContainer(test()->getTestWebP(), 'test.webp');

    $pushedJobs = Queue::pushed(GenerateAltTextJob::class);

    expect($pushedJobs->count())->toBe(4);
});

it('does not dispatch jobs for other common file types', function () {
    Queue::fake();
    config()->set('statamic.aida.generate_on_upload', true);

    // Upload invalid image/file types
    test()->uploadTestImageToTestContainer(test()->getTestSvg(), 'test.svg');
    test()->uploadTestImageToTestContainer(test()->getTestZip(), 'test.zip');
    test()->uploadTestImageToTestContainer(test()->getTestPdf(), 'test.pdf');

    $pushedJobs = Queue::push(GenerateAltTextJob::class);

    expect($pushedJobs)->toBe(null);
});
