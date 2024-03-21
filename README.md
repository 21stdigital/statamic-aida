# A.I.D.A - AI-Driven Alt-text Assistant

> AI-Driven Alt-text Assistant for Statamic 4

This addon generates alt texts for images with the help of AI.

## Why?

This addon simplifies the process of adding alt text to images by using AI to automatically generate them, reducing the manual effort and resources traditionally required. It improves web accessibility for visually impaired users, making content more inclusive. Additionally, it enhances SEO by helping search engines understand and index images better. Its capability to support multiple languages means it is equally beneficial for simple and small websites as well as large, multilingual platforms, making it a versatile and efficient tool for enhancing web content.

## Installation

Install the addon by using composer:

`composer require tfd/statamic-aida`

## Configuration

Publish the addon's configuration file:

`php artisan vendor:publish --tag="aida-config"`

### Queue

It is highly recommended to use a queue connection like redis for handling the alt text generation. For general information read the [Laravel documentation](https://laravel.com/docs/queues). This is especially useful if you want to generate alt texts for multiple images at once.

The addon uses the `default` queue, but you can define a custom one by changing the `queue` value on the `aida.php` configuration file or by setting an environment variable: `GENERATE_ALT_TEXT_QUEUE=redis`.

### Generate On Upload

By default the automatic alt text generation is disabled when uploading new assets. If you want to use it, change the `generate_on_upload` value in the `aida.php` configuration file or set an environment variable: `GENERATE_ALT_TEXT_ON_UPLOAD=true`.

## Action

To manually generate alt texts for custom images, a new action **Generate Alt Text** is available in the Asset Browser. Select the assets you wish to generate alt texts for and choose the **Generate Alt Text** action from the actions panel:

![Screenshot](./docs/action.png)

In the confirmation dialog you can decide if you want to override existing alt texts.

## Custom Generator

If you do not want to use the included OpenAI based generator but your own, there are 2 steps necessary:

### 1. Create Custom Generator

Create a custom generator class in your app directory: `/app/Generator/MyAltTextGenerator.php`. This class has to implement the interface `TFD\AIDA\Generator\Generator` and its `generate` method:

```php
// app/Generator/MyAltTextGenerator.php

<?php

namespace App\Generator;

use \Statamic\Assets\Asset;
use TFD\AIDA\Generator\Generator;

class MyAltTextGenerator implements Generator
{
    /**
     * @param Asset $asset
     * @param string $language
     * @return string
     */
    public function generate($asset, $locale = 'en')
    {
        /**
         * Use some other service to get the alt text from the asset.
         * Depending on the service, you might have to transform the asset object
         * and use its url or base64 encoded string.
         */
        $altText = SomeApi::get($asset, $locale);

        return $altText;
    }
}

?>
```

### 2. Update Configuration  

Make sure, the addon's configuration file is published:

`php artisan vendor:publish --tag="aida-config"`

Update the generator value in the `aida.php` configuration file:

```php
// config/statamic/aida.php

<?php

use App\Generator\MyAltTextGenerator;

return [
    // ...

    'generator' => MyAltTextGenerator::class,

    // ...
];

?>
```
