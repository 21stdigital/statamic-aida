<?php

namespace TFD\AIDA\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Statamic\Assets\Asset as AssetsAsset;
use Statamic\Facades\Asset;
use TFD\AIDA\Generator\Generator;

class GenerateAltTextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    protected $assetId;

    /** @var array */
    protected $altFieldMappings;

    public function __construct(string $assetId, array $altFieldMappings)
    {
        $this->assetId = $assetId;
        $this->altFieldMappings = $altFieldMappings;

        $this->queue = config('statamic.aida.queue');
    }

    public function handle(Generator $generator): void
    {
        /** @var AssetsAsset|null */
        $asset = Asset::findById($this->assetId);

        if ($asset === null) {
            Log::info("Could not generate alt text for \"{$this->assetId}\", because the asset could not be found.");

            return;
        }

        foreach ($this->altFieldMappings as $locale => $fieldName) {
            Log::debug(sprintf(
                'Generating alt text for "%s" in "%s" for field "%s".',
                $this->assetId,
                $locale,
                $fieldName
            ));

            $result = $generator->generate($asset, $locale);

            $asset->set($fieldName, $result);
        }

        $asset->save();
    }
}
