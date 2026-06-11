<?php

namespace App\Services;

class ProfanityFilterService
{
    protected array $badWords = [
        'anjing', 'babi', 'monyet', 'bangsat', 'tolol', 'goblok', 'bego', 
        'kampret', 'sialan', 'bajingan', 'jancok', 'asu', 'kontol', 'memek',
        'ngentot', 'perek', 'pelacur'
    ];

    /**
     * Sensor teks yang mengandung kata-kata kasar dengan tanda bintang ***
     *
     * @param string $text
     * @return string
     */
    public function filter(string $text): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        foreach ($this->badWords as $word) {
            // Case-insensitive replacement menggunakan regex
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $text = preg_replace_callback($pattern, function ($matches) {
                return str_repeat('*', strlen($matches[0]));
            }, $text);
        }

        return $text;
    }
}
