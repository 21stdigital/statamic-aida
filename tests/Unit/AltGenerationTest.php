<?php

use Illuminate\Support\Facades\Queue;
use Statamic\Assets\Asset;
use Statamic\Facades\Asset as FacadesAsset;
use TFD\AIDA\Generator\DummyGenerator;
use TFD\AIDA\Jobs\GenerateAltTextJob;

beforeEach(function () {
    $this->generator = new DummyGenerator;
    $this->asset = new Asset;
});

it('generates an alt text', function () {
    $altText = $this->generator->generate($this->asset, 'en');

    expect($altText)->toBeString();
});

it('generates language aware alt texts', function () {
    $altTextEn = $this->generator->generate($this->asset, 'en');
    $altTextDe = $this->generator->generate($this->asset, 'de');

    expect($altTextEn)->toBeString();
    expect($altTextDe)->toBeString();
    expect($altTextEn)->not->toBe($altTextDe);
});

it('saves a generated alt text on an asset', function () {
    Queue::fake();

    $altText = $this->generator->generate($this->asset, 'en');
    config()->set('statamic.aida.generate_on_upload', true);

    $asset = test()->uploadTestImageToTestContainer();
    $pushedJobs = Queue::pushed(GenerateAltTextJob::class);

    // Actually execute the queued import job
    $job = $pushedJobs->first();
    $job->handle($this->generator);

    // After exeucting the import job, the alt text should be generated for the image.
    $assetWithAlt = FacadesAsset::findById($asset->id());
    expect($assetWithAlt->alt)->toBe($altText);
});
