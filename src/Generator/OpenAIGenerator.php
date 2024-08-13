<?php

namespace TFD\AIDA\Generator;

use Locale;
use OpenAI;

class OpenAIGenerator implements Generator
{
    /**
     * {@inheritDoc}
     */
    public function generate($asset, $locale = 'en')
    {
        $client = OpenAI::client(config('statamic.aida.open_ai_key'));

        $encodedImage = base64_encode($asset->contents());
        $mimeType = $asset->mimeType();

        // Convert locale to language, e. g.: 'en' -> 'English'
        $language = Locale::getDisplayName($locale, 'en');

        $payload = [
            'model' => config('statamic.aida.config.model'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => "You are a specialist generating alt text for images. Answer short and descriptive, like a tweet. Do not include 'image of' or 'picture of'. Capitalize the first letter and end whole sentences with a period. Answer in {$language}.",
                        ],
                    ],
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Generate an alt text for the attached image.',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$encodedImage}",
                                'detail' => config('statamic.aida.config.image_detail'),
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => config('statamic.aida.config.max_tokens'),
        ];

        $response = $client->chat()->create($payload);

        // TODO: The response could be incomplete, e. g. when the 'finishReason' is not 'stop', probably due to a token limitation or usage limit. These cases need to be handled.
        $result = trim($response->choices[0]->message->content);

        // Sanitize the result to convert special characters into HTML entities, preventing invalid HTML code.
        $sanitizedResult = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');

        return $sanitizedResult;
    }
}
