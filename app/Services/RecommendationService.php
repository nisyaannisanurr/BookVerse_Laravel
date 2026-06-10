<?php

namespace App\Services;

use App\Models\AgregasiRekomendasi;
use App\Models\Buku;
use App\Models\LogPerilakuUser;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Log user behavior and auto-aggregate
     */
    public function logBehavior(int $userId, string $type, int $weight, ?string $genre = null, ?string $metadata = null): void
    {
        LogPerilakuUser::create([
            'user_id'         => $userId,
            'tipe_aktivitas'  => $type,
            'bobot'           => $weight,
            'genre_terkait'   => $genre,
            'metadata'        => $metadata,
        ]);

        // Auto-aggregate setiap kali ada aktivitas baru
        $this->aggregateForUser($userId);
    }

    /**
     * Get user's top genres
     */
    public function getTopGenres(int $userId, int $limit = 3)
    {
        return AgregasiRekomendasi::where('user_id', $userId)
            ->orderByDesc('total_skor')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommended books — balanced mix from top 3 genres
     */
    public function getRecommendedBooks(int $userId, int $limit = 12)
    {
        // Get top 3 genres from aggregation
        $topGenres = AgregasiRekomendasi::where('user_id', $userId)
            ->orderByDesc('total_skor')
            ->limit(3)
            ->pluck('genre')
            ->toArray();

        if (empty($topGenres)) {
            return collect();
        }

        // IDs already rated or shelved (exclude these)
        $excludedIds = DB::table('rating_buku')->where('user_id', $userId)->pluck('buku_id')
            ->merge(DB::table('rak_buku_user')->where('user_id', $userId)->pluck('buku_id'))
            ->unique()->toArray();

        // Fetch max 4 books from each top genre — for diversity
        $perGenre = max(2, intval($limit / count($topGenres)));
        $result = collect();

        foreach ($topGenres as $genre) {
            $books = Buku::withRating()
                ->where('buku.genre_buku', $genre)
                ->whereNotIn('buku.id', $excludedIds)
                ->orderByDesc('avg_rating')
                ->limit($perGenre + 2)
                ->get();
            $result = $result->concat($books->shuffle()->take($perGenre));
        }

        // Shuffle to interleave genres, then limit
        return $result->shuffle()->take($limit);
    }

    /**
     * Check if user has enough behavior data for AI
     */
    public function hasUserData(int $userId): bool
    {
        // Check aggregated data first
        if (AgregasiRekomendasi::where('user_id', $userId)->exists()) {
            return true;
        }

        // Fallback: check raw logs (dan trigger aggregation jika ada)
        $hasLogs = LogPerilakuUser::where('user_id', $userId)
            ->whereNotNull('genre_terkait')
            ->exists();

        if ($hasLogs) {
            $this->aggregateForUser($userId);
            return true;
        }

        return false;
    }

    /**
     * Aggregate recommendations for user — FRESH re-calculation
     */
    public function aggregateForUser(int $userId): void
    {
        // Calculate scores from raw logs (last 60 days)
        $genres = LogPerilakuUser::where('user_id', $userId)
            ->whereNotNull('genre_terkait')
            ->where('tanggal_aktivitas', '>=', now()->subDays(60))
            ->groupBy('genre_terkait')
            ->selectRaw('genre_terkait, SUM(bobot) as total')
            ->orderByDesc('total')
            ->get();

        if ($genres->isEmpty()) return;

        // DELETE stale data, then re-insert fresh scores
        AgregasiRekomendasi::where('user_id', $userId)->delete();

        $today = date('Y-m-d');
        foreach ($genres as $genre) {
            AgregasiRekomendasi::create([
                'user_id'      => $userId,
                'genre'        => $genre->genre_terkait,
                'total_skor'   => $genre->total,
                'periode_mulai' => $today,
            ]);
        }
    }
}
