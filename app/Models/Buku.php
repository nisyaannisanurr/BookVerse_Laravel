<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';

    // Tabel buku hanya punya created_at, tidak ada updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'judul',
        'penulis',
        'sinopsis',
        'cover_buku',
        'genre_buku',
    ];

    // Relationships
    public function ratings()
    {
        return $this->hasMany(RatingBuku::class, 'buku_id');
    }

    public function rakBukuUser()
    {
        return $this->hasMany(RakBukuUser::class, 'buku_id');
    }

    // Scopes
    public function scopeWithRating($query)
    {
        return $query->selectRaw('buku.*, COALESCE(AVG(rating_buku.skor_rating), 0) as avg_rating, COUNT(rating_buku.id) as total_rating')
            ->leftJoin('rating_buku', 'rating_buku.buku_id', '=', 'buku.id')
            ->groupBy('buku.id');
    }

    public function scopeByGenre($query, ?string $genre)
    {
        if ($genre && $genre !== 'Semua') {
            return $query->where('buku.genre_buku', $genre);
        }
        return $query;
    }

    // Static helpers
    public static function getGenres(): array
    {
        return self::distinct()->orderBy('genre_buku')->pluck('genre_buku')->toArray();
    }

    public static function search(string $keyword)
    {
        $like = '%' . $keyword . '%';
        return self::withRating()
            ->where(function ($q) use ($like) {
                $q->where('buku.judul', 'LIKE', $like)
                  ->orWhere('buku.penulis', 'LIKE', $like);
            })
            ->orderByDesc('avg_rating');
    }

    public static function getPopularThisWeek(int $limit = 5)
    {
        $results = self::selectRaw('buku.*, AVG(rating_buku.skor_rating) as avg_rating, COUNT(rating_buku.id) as total_rating')
            ->join('rating_buku', 'rating_buku.buku_id', '=', 'buku.id')
            ->where('rating_buku.tanggal_rating', '>=', now()->subDays(7))
            ->groupBy('buku.id')
            ->orderByDesc('avg_rating')
            ->orderByDesc('total_rating')
            ->limit($limit)
            ->get();

        if ($results->count() < $limit) {
            $results = self::withRating()
                ->orderByDesc('avg_rating')
                ->orderByDesc('total_rating')
                ->limit($limit)
                ->get();
        }

        return $results;
    }

    public static function getTopRated(?string $genre = null, int $limit = 10)
    {
        return self::withRating()
            ->byGenre($genre)
            ->havingRaw('total_rating > 0')
            ->orderByDesc('avg_rating')
            ->orderByDesc('total_rating')
            ->limit($limit)
            ->get();
    }
}
