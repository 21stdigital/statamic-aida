<?php

use TFD\AIDA\Generator\OpenAIGenerator;

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | The key is used for making API requests to OpenAI.
    |
    */
    'open_ai_key' => env('OPEN_AI_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Generator Class
    |--------------------------------------------------------------------------
    |
    | The OpenAI API is used by default to generate alt texts.
    | If you want to use your own generator class, you can define it here.
    |
    */

    'generator' => OpenAIGenerator::class,

    /*
    |--------------------------------------------------------------------------
    | Custom Queue
    |--------------------------------------------------------------------------
    |
    | Define a custom queue that is responsible for handling
    | the alt text generation jobs.
    |
    */

    'queue' => env('GENERATE_ALT_TEXT_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Generate On Upload
    |--------------------------------------------------------------------------
    |
    | Whether alt texts should be generated on upload.
    |
    */

    'generate_on_upload' => env('GENERATE_ALT_TEXT_ON_UPLOAD', false),

    /*
    |--------------------------------------------------------------------------
    | Alt Field Mapping
    |--------------------------------------------------------------------------
    |
    | By default, the addon assumes the alt field is named "alt".
    | If your alt field has a custom field name, define it here by creating an array,
    | where the key is the language of your site and the value is the alt field name.
    |
    | In multilingual sites you can also define all languages of your sites, for which
    | an alt text should be generated. If you do not define the alt field mapping here,
    | the addon assumes that your alt fields are suffixed with the language,
    | e. g. `alt_en` or `alt_fr`.
    |
    */
    'alt_field_mapping' => [
        // 'en' => 'custom_alt_field',

        // 'de' => 'alt_de',
        // 'fr' => 'alt_fr',
    ],
];
