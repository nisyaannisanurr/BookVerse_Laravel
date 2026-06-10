<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    protected RecommendationService $recService;

    public function __construct(RecommendationService $recService)
    {
        $this->recService = $recService;
    }

    public function index(Request $request)
    {
        $genre  = $request->query('genre');
        $search = trim($request->query('search', ''));
        $genres = Buku::getGenres();

        // AI Recommendations (based on user behavior)
        $recommendations = collect();
        $topGenres       = collect();
        $hasRecData      = false;

        if (auth()->check()) {
            $userId = auth()->id();
            $hasRecData = $this->recService->hasUserData($userId);
            if ($hasRecData) {
                $recommendations = $this->recService->getRecommendedBooks($userId);
                $topGenres       = $this->recService->getTopGenres($userId);
            }

            // Log perilaku CARI: catat genre yang dicari/difilter
            if ($genre) {
                // User memilih filter genre → log sebagai pencarian genre (bobot 2)
                $this->recService->logBehavior($userId, 'cari', 2, $genre, $search ?: null);
            } elseif ($search !== '') {
                // User search keyword → cari genre buku yang relevan dari hasil pencarian
                $firstMatch = Buku::where('judul', 'LIKE', "%{$search}%")
                    ->orWhere('penulis', 'LIKE', "%{$search}%")
                    ->first();
                if ($firstMatch && $firstMatch->genre_buku) {
                    $this->recService->logBehavior($userId, 'cari', 1, $firstMatch->genre_buku, $search);
                }
            }
        }

        // Top rated books
        $topRated = Buku::getTopRated($genre);

        // All books with optional search + genre + sort filter
        $sort  = $request->query('sort', 'rating');
        $query = Buku::withRating()->byGenre($genre);
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('buku.judul', 'LIKE', $like)
                  ->orWhere('buku.penulis', 'LIKE', $like);
            });
        }

        // Apply sort
        match($sort) {
            'terbaru' => $query->orderByDesc('buku.created_at'),
            'judul'   => $query->orderBy('buku.judul'),
            'ulasan'  => $query->orderByDesc('total_rating')->orderByDesc('avg_rating'),
            default   => $query->orderByDesc('avg_rating')->orderByDesc('total_rating'),
        };

        $books = $query->get();

        return view('recommendations.index', compact(
            'books', 'genres', 'search', 'sort',
            'recommendations', 'topGenres', 'hasRecData', 'topRated'
        ));
    }
}
