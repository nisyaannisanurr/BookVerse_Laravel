<?php

namespace App\Services;

use App\Models\Profanity;
use Illuminate\Support\Facades\Cache;

class ProfanityFilterService
{
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

        $badWords = Cache::rememberForever('profanity_words', function () {
            // Jika tabel belum ada atau kosong, kembalikan array kosong (mencegah error saat migrate awal)
            try {
                return Profanity::pluck('kata')->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        if (empty($badWords)) {
            return $text;
        }

        foreach ($badWords as $word) {
            // Case-insensitive replacement menggunakan regex
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $text = preg_replace_callback($pattern, function ($matches) {
                return str_repeat('*', strlen($matches[0]));
            }, $text);
        }

        return $text;
    }
}
