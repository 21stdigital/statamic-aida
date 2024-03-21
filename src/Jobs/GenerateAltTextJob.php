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

    /** @var string */
    protected $language;

    /** @var string */
    protected $altFieldName;

    public function __construct(string $assetId, string $language, string $altFieldName)
    {
        $this->assetId = $assetId;
        $this->language = $language;
        $this->altFieldName = $altFieldName;

        $this->queue = config('statamic.aida.queue');
    }

    public function handle(Generator $generator): void
    {
        Log::debug(sprintf(
            'Generating alt text for "%s" in "%s" for field "%s".',
            $this->assetId,
            $this->language,
            $this->altFieldName
        ));

        /** @var AssetsAsset|null */
        $asset = Asset::findById($this->assetId);
        if ($asset === null) {
            Log::info(sprintf(
                'Could not generate alt text for "%s" in "%s" for field "%s", because the asset could not be found.',
                $this->assetId,
                $this->language,
                $this->altFieldName
            ));

            return;
        }

        $result = $generator->generate($asset, $this->language);
        $asset
            ->set($this->altFieldName, $result)
            ->save();
    }
}
