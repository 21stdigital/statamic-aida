<?php

namespace TFD\AIDA\Generator;

class DummyGenerator implements Generator
{
    public function generate($asset, $locale = 'en')
    {
        $altTexts = [
            'en' => 'I am an english alt text.',
            'de' => 'Ich bin der deutsche alt Text.',
            'fr' => 'Je suis un texte alternatif français.',
        ];

        if (array_key_exists($locale, $altTexts)) {
            return $altTexts[$locale];
        }

        return $altTexts['en'];
    }
}
