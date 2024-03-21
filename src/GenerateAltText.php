<?php

namespace TFD\AIDA;

use Illuminate\Support\Str;
use Statamic\Assets\Asset;
use Statamic\Facades\Site;
use TFD\AIDA\Jobs\GenerateAltTextJob;

class GenerateAltText
{
    /**
     * @var Asset
     */
    protected $asset;

    /**
     * @var bool
     */
    protected $overwrite;

    public function __construct($asset, $overwrite = false)
    {
        $this->asset = $asset;
        $this->overwrite = $overwrite;
    }

    public function generate(): void
    {
        $altFieldMappings = ! empty(config('statamic.aida.alt_field_mapping')) ? config('statamic.aida.alt_field_mapping') : $this->generateDefaultAltFieldMappings();
        foreach ($altFieldMappings as $locale => $fieldName) {
            // Skip generation if overwriting is disabled and the asset already has an alt text
            if (! $this->overwrite && $this->assetHasAltText($this->asset, $fieldName)) {
                continue;
            }

            GenerateAltTextJob::dispatch($this->asset->id(), $locale, $fieldName);
        }
    }

    /**
     * Check if the asset's alt field already contains text.
     *
     * @param  Asset  $asset
     * @param  string  $altField
     * @return bool
     */
    protected function assetHasAltText($asset, $altField)
    {
        $existingAltText = $asset->get($altField);

        return Str::of($existingAltText)->isNotEmpty();
    }

    /**
     * Generate the default alt field mapping for the sites languages.
     *
     * @return array<string, string>
     */
    protected function generateDefaultAltFieldMappings()
    {
        $sites = Site::all();

        // Get all languages from all sites
        $languages = $sites
            ->map(function ($site) {
                return $site->lang();
            })
            ->values()
            ->unique();

        /**
         * If there is only a single language, there is no reason to rename the `alt` field,
         * so we use the default field name from statamic.
         */
        if ($languages->count() === 1) {
            return [
                $languages->first() => 'alt',
            ];
        }

        /**
         * Otherwise, assume that for every language a corresponding `alt_<lang>` field exists, e.g.:
         * 'en' > 'alt_en'
         * 'de' > 'alt_de'
         * 'fr' > 'alt_fr'
         */
        $altFieldMappings = $languages
            ->flatMap(function ($language) {
                return [
                    $language => "alt_{$language}",
                ];
            })
            ->toArray();

        return $altFieldMappings;
    }
}
