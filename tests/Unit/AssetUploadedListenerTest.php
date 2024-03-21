<?php

use Illuminate\Support\Facades\Queue;
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

it('creates one job per site with default alt text field settings', function () {
    Queue::fake();

    config()->set('statamic.aida.generate_on_upload', true);
    test()->useMultiSite();

    $this->asset = test()->uploadTestImageToTestContainer();

    $pushedJobs = Queue::pushed(GenerateAltTextJob::class);

    expect($pushedJobs->count())->toBe(3);
    Queue::pushedJobs(GenerateAltTextJob::class, 3);
});

it('creates one job per configured alt text field mapping', function () {
    Queue::fake();

    config()->set('statamic.aida.generate_on_upload', true);

    config()->set('statamic.aida.alt_field_mapping', [
        'en' => 'alt_en',
        'de' => 'alt_de',
        'fr' => 'alt_fr',
    ]);

    $this->asset = test()->uploadTestImageToTestContainer();

    $pushedJobs = Queue::pushed(GenerateAltTextJob::class);

    expect($pushedJobs->count())->toBe(3);
    Queue::assertPushed(GenerateAltTextJob::class, 3);
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
