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
    | Default configuration
    |--------------------------------------------------------------------------
    |
    | Define configuration values that are used by the default OpenAIGenerator class.
    |
    */

    'config' => [
        /**
         * Define the model that is used to process the images and generate alt texts.
         *
         * Only gpt-4 models are supported, e.g.:
         * - gpt-4o
         * - gpt-4o-mini
         * - gpt-4-turbo
         *
         * @see https://platform.openai.com/docs/models
         */
        'model' => env('OPEN_AI_MODEL', 'gpt-4o-mini'),

        /**
         * The `max_tokens` parameter in OpenAI API requests limits the maximum number of tokens in the response.
         * Tokens can be as short as one character or as long as one word (e.g., "a", "openai").
         * Setting `max_tokens` helps control the length of the output, manage costs, and reduce latency.
         * For example, `max_tokens=50` ensures the response does not exceed 50 tokens.
         * Adjust the value based on the desired level of detail and brevity for your application.
         */
        'max_tokens' => env('OPEN_AI_MAX_TOKENS', 200),

        /**
         * By controlling the detail parameter, which has three options, low, high, or auto, you have control over
         * how the model processes the image and generates its textual understanding. The auto setting  will look at
         * the image input size and decide if it should use the low or high setting.
         * Adjust the value to balance image understanding quality with performance and cost.
         *
         * @see https://platform.openai.com/docs/guides/vision/low-or-high-fidelity-image-understanding
         */
        'image_detail' => env('OPEN_AI_IMAGE_DETAIL', 'low'),
    ],

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
