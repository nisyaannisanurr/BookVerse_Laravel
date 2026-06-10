<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class BookVerseHelper
{
    /**
     * Format date in Indonesian format: "04 Juni 2026"
     */
    public static function formatTanggal(?string $datetime): string
    {
        if (!$datetime) return '-';

        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',      6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $ts = strtotime($datetime);
        $day = date('d', $ts);
        $month = (int)date('m', $ts);
        $year = date('Y', $ts);

        return $day . ' ' . $bulan[$month] . ' ' . $year;
    }

    /**
     * Format date with time: "04 Juni 2026, 14:30"
     */
    public static function formatTanggalWaktu(?string $datetime): string
    {
        if (!$datetime) return '-';
        $ts = strtotime($datetime);
        return self::formatTanggal($datetime) . ', ' . date('H:i', $ts);
    }

    /**
     * Format relative time: "2 jam lalu"
     */
    public static function timeAgo(?string $datetime): string
    {
        if (!$datetime) return '-';

        $now = time();
        $ts = strtotime($datetime);
        $diff = $now - $ts;

        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
        if ($diff < 604800) return floor($diff / 86400) . ' hari lalu';

        return self::formatTanggal($datetime);
    }

    /**
     * Format price in Indonesian Rupiah
     */
    public static function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Generate star rating HTML
     */
    public static function starRating(float $rating, bool $showNumber = true): string
    {
        $html = '<span class="star-rating">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= floor($rating)) {
                $html .= '<span class="star filled">★</span>';
            } elseif ($i - $rating < 1 && $i - $rating > 0) {
                $html .= '<span class="star half">★</span>';
            } else {
                $html .= '<span class="star empty">☆</span>';
            }
        }
        if ($showNumber) {
            $html .= ' <span class="rating-number">' . number_format($rating, 1) . '</span>';
        }
        $html .= '</span>';
        return $html;
    }

    /**
     * Get status badge HTML
     */
    public static function statusBadge(string $status): string
    {
        $classes = [
            'pending'       => 'badge-warning',
            'aktif'         => 'badge-success',
            'approved'      => 'badge-success',
            'nonaktif'      => 'badge-danger',
            'rejected'      => 'badge-danger',
            'tersedia'      => 'badge-success',
            'terjual'       => 'badge-info',
            'ditangguhkan'  => 'badge-danger',
            'sedang_dibaca' => 'badge-info',
            'selesai'       => 'badge-success',
            'wishlist'      => 'badge-warning',
        ];

        $labels = [
            'pending'       => 'Pending',
            'aktif'         => 'Aktif',
            'approved'      => 'Disetujui',
            'nonaktif'      => 'Nonaktif',
            'rejected'      => 'Ditolak',
            'tersedia'      => 'Tersedia',
            'terjual'       => 'Terjual',
            'ditangguhkan'  => 'Ditangguhkan',
            'sedang_dibaca' => 'Sedang Dibaca',
            'selesai'       => 'Selesai',
            'wishlist'      => 'Wishlist',
        ];

        $class = $classes[$status] ?? 'badge-secondary';
        $label = $labels[$status] ?? $status;

        return '<span class="badge ' . $class . '">' . e($label) . '</span>';
    }

    /**
     * Generate WhatsApp chat link
     */
    public static function generateWhatsAppLink(string $phone, string $bookTitle): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        $text = urlencode("Halo, saya tertarik dengan buku \"{$bookTitle}\" yang dijual di BookVerse. Apakah masih tersedia?");
        return "https://wa.me/{$phone}?text={$text}";
    }

    /**
     * Get upload URL for files
     */
    public static function uploadUrl(string $dir, ?string $filename): string
    {
        if (!$filename) {
            return asset('images/placeholder.png');
        }
        return asset('uploads/' . trim($dir, '/') . '/' . $filename);
    }

    /**
     * Truncate text
     */
    public static function truncate(string $text, int $length = 100): string
    {
        return Str::limit($text, $length);
    }
}
