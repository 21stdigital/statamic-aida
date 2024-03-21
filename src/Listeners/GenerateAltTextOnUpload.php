<?php

namespace TFD\AIDA\Listeners;

use Statamic\Events\AssetUploaded;
use TFD\AIDA\GenerateAltText;

class GenerateAltTextOnUpload
{
    public function handle(AssetUploaded $event): void
    {
        if (! $event->asset->isImage()) {
            return;
        }

        if ($event->asset->extension() === 'svg') {
            return;
        }

        if (! config('statamic.aida.generate_on_upload', false)) {
            return;
        }

        $generateAltText = new GenerateAltText($event->asset);
        $generateAltText->generate();
    }
}
