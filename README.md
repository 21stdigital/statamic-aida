<!-- statamic:hide -->
<h1 align="center">
  <img src="./docs/aida-logo-solo.svg" alt="AIDA Logo" width="200">
  <br>
  A.I.D.A - AI-Driven Alt-text Assistant
</h1>
<!-- /statamic:hide -->

<p align="center">
  <a href="https://statamic.com" style="text-decoration: none">
    <img src="https://img.shields.io/badge/Statamic-5.0%2B-FF269E?style=flat-square" alt="Statamic 5.0+" />
  </a>
  <a href="https://github.com/21stdigital/statamic-aida/releases" style="text-decoration: none">
    <img src="https://img.shields.io/github/v/release/21stdigital/statamic-aida?label=Release&style=flat-square" alt="Latest Version" />
  </a>
  <a href="https://github.com/21stdigital/statamic-aida/actions/workflows/tests.yml?query=branch%3Amain" style="text-decoration: none">
    <img src="https://img.shields.io/github/actions/workflow/status/21stdigital/statamic-aida/tests.yml?branch=main&style=flat-square&label=Tests" />
  </a>
  <a href="https://packagist.org/packages/tfd/statamic-aida" style="text-decoration: none">
    <img src="https://img.shields.io/packagist/dt/tfd/statamic-aida?style=flat-square&label=Downloads" />
  </a>
</p>

> Enhancing web accessibility and SEO through AI-generated alt texts for Statamic 5.

A.I.D.A is an addon for Statamic 5 that leverages AI to automatically generate descriptive alt texts for images. By simplifying this process, A.I.D.A aids in making web content more accessible to visually impaired users, enhances SEO, and supports content in multiple languages.

## Features

- **AI-Powered:** Utilizes AI to craft meaningful and contextually relevant alt texts.
- **Web Accessibility:** Improves accessibility for visually impaired users by providing descriptive image texts.
- **SEO Enhancement:** Boosts SEO by enabling search engines to better understand and index website images.
- **Multilingual Support:** Equally effective for both monolingual and multilingual websites.
- **Efficiency:** Saves time and resources by reducing manual effort required for writing alt texts.

## Getting Started

### Installation

1. Install the addon using Composer:

   ```bash
   composer require tfd/statamic-aida
   ```

### Configuration

To set up and customize the A.I.D.A addon, follow these steps:

1. **Publish Configuration File:** Start by publishing the addon's configuration file to make it editable. Run the following command in your terminal:

   ```bash
   php artisan vendor:publish --tag="aida-config"
   ```

   This will copy the default configuration file to your application's `config` directory, allowing you to customize the addon settings.

2. **Set OpenAI API Key:** A.I.D.A uses OpenAI to generate alt texts. You must provide your OpenAI API key for the service to function. It's recommended to set this key in your application's `.env` file for security reasons, rather than directly in the `aida.php` configuration file:

   Add the following line to your `.env` file:

   ```plaintext
   OPEN_AI_API_KEY=your_openai_api_key_here
   ```

   This ensures your API key remains secure and not hard-coded in your version-controlled files.

3. **Configure Queue:** To manage performance and efficiently handle multiple alt text generation requests, it's advisable to use a queue connection, such as Redis. Queues allow for asynchronous processing, improving the user experience and system performance.

   First, ensure you have your queue system set up according to the [Laravel documentation](https://laravel.com/docs/queues). Then, specify your queue connection for A.I.D.A by adding the following line to your `.env` file:

   ```plaintext
   GENERATE_ALT_TEXT_QUEUE=redis
   ```

   This tells A.I.D.A to use the `redis` queue connection for processing alt text generation jobs.

4. **Generator Configuration**: The default OpenAI Generator is configured with a sensible set of default values. You can adjust these values to better fit your needs.

   The following options are configurable via `.env` file:

   ```plaintext
   # Define the model that is used to process the images and generate alt texts. Only gtp-4 models are supported.
   OPEN_AI_MODEL=gpt-4o-mini

   # Limit the number of tokens that are used in the response.
   OPEN_AI_MAX_TOKENS=200

   # Adjust the value to balance image understanding quality with performance and cost.
   OPEN_AI_IMAGE_DETAIL=low
   ```

   For additional information please have a look at the `config/aida.php` file.

5. **Automatic Generation on Upload:** By default, the addon does not generate alt texts automatically upon image uploads to avoid unnecessary processing. However, you can enable this feature to have alt texts generated immediately as images are uploaded.

   To enable automatic alt text generation on upload, add the following line to your `.env` file:

   ```plaintext
   GENERATE_ALT_TEXT_ON_UPLOAD=true
   ```

   With this setting enabled, every new image uploaded will automatically have an alt text generated, enhancing accessibility and SEO with minimal effort.

### Example Configurations

#### Single Site with no custom alt field

- single site with handle `en`
- asset alt field has default handle `alt`

```php
// config/statamic/aida.php
<?php

return [

   // No configuration necessary
   'alt_field_mapping' => []

];
```

#### Single Site with custom alt field

- single site with handle `en`
- asset alt field has a custom handle `my_custom_alt`

```php
// config/statamic/aida.php
<?php

return [

   // Custom configuration necessary due to custom alt field handle
   'alt_field_mapping' => [
      'en' => 'my_custom_alt'
   ]

];
```

#### Multi Site (1)

- multiple sites with handles `en`, `fr`, `de`
- assets have a custom alt field for every language
  - `alt_en`
  - `alt_fr`
  - `alt_de`

```php
// config/statamic/aida.php
<?php

return [

   /**
    * No configuration necessary; the addon automatically matches all availables site handles
    * to a field `alt_<handle>`, e. g. `alt_en` or `alt_fr`.
    */
   'alt_field_mapping' => []

];
```

#### Multi Site (2)

- multiple sites with handles `en`, `fr`, `de`
- assets have a custom alt field for every language
  - `alt_english`
  - `alt_french`
  - `alt_german`

```php
// config/statamic/aida.php
<?php

return [

   /**
    * Custom configuration is necessary, because the alt field handles differ from the ones the addon assumes
    */
   'alt_field_mapping' => [
      'en' => 'alt_english',
      'fr' => 'alt_french',
      'de' => 'alt_german',
   ]

];
```

#### Multi Site (3)

- multiple sites with handles `en`, `fr`, `de`
- assets only have alt fields for a subset of languages:
  - `alt_en`
  - `alt_de`

```php
// config/statamic/aida.php
<?php

return [

   /**
    * Custom configuration defines, for which languages the alt text is being generated.
    */
   'alt_field_mapping' => [
      'en' => 'alt_en',
      'de' => 'alt_de',
   ]

];
```

### Updates

After updating the addon, make sure to inspect the `config/aida.php` file to learn about new configuration values. You might want to add them to the published config file of your application.

### Usage

**Manual Generation:** Use the **Generate Alt Text** action in the Asset Browser to generate alt texts for selected images. This option allows you to decide whether to override existing alt texts.

![Screenshot of Generate Alt Text action](./docs/action.png)

### Customization

**Custom Generator:** You can replace the default OpenAI-based generator with your own by implementing the `TFD\AIDA\Generator\Generator` interface.

1. Create a custom generator class e.g. `/app/Generator/MyAltTextGenerator.php`.

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

         /**
          * You might want to sanitize the altText with `htmlspecialchars($altText, ENT_QUOTES, 'UTF-8')` to prevent invalid HTML code.
          * Alternatively you can use the `sanitize` modifier in your view files.
          */

         return $altText;
      }
   }
```

2.  Implement the `generate` method to utilize your preferred service for generating alt texts.
3.  Update the `aida.php` configuration file to use your custom generator class.

```php
// config/statamic/aida.php

<?php

use App\Generator\MyAltTextGenerator;

return [
   // ...

   'generator' => MyAltTextGenerator::class,

   // ...
];
```

## Credits

This project is maintained by 21st digital. We appreciate the contributions from the community that help make this project better.
