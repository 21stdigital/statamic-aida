<?php

namespace TFD\AIDA\Generator;

use Statamic\Assets\Asset;

interface Generator
{
    /**
     * Generate an alt text for a given asset and locale.
     *
     * @param  Asset  $asset
     * @param  string  $locale
     * @return string
     */
    public function generate($asset, $locale = 'en');
}
