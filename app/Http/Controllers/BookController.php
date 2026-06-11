<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\RakBukuUser;
use App\Models\RatingBuku;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected RecommendationService $recService;

    public function __construct(RecommendationService $recService)
    {
        $this->recService = $recService;
    }

    public function index(Request $request)
    {
        $genre = $request->get('genre');
        $genres = Buku::getGenres();
        $books = Buku::withRating()->byGenre($genre)->orderByDesc('buku.created_at')->limit(60)->get();
        $topRated = Buku::getTopRated($genre);

        $recommendations = collect();
        $topGenres = collect();
        $hasRecData = false;

        if (auth()->check()) {
            $hasRecData = $this->recService->hasUserData(auth()->id());
            if ($hasRecData) {
                $recommendations = $this->recService->getRecommendedBooks(auth()->id());
                $topGenres = $this->recService->getTopGenres(auth()->id());
            }
        }

        return view('books.index', compact('genre', 'genres', 'books', 'topRated', 'recommendations', 'topGenres', 'hasRecData'));
    }

    public function detail(int $id)
    {
        $book = Buku::withRating()->where('buku.id', $id)->first();
        if (!$book) abort(404);

        $reviews = RatingBuku::where('buku_id', $id)
            ->with('user:id,username,foto_profil')
            ->orderByDesc('tanggal_rating')
            ->get();

        $userRating = null;
        $shelfEntry = null;

        if (auth()->check()) {
            $userRating = RatingBuku::where('user_id', auth()->id())->where('buku_id', $id)->first();
            $shelfEntry = RakBukuUser::where('user_id', auth()->id())->where('buku_id', $id)->first();

            $this->recService->logBehavior(auth()->id(), 'klik_detail', 2, $book->genre_buku, $book->judul);
        }

        // Buku Serupa: same genre, exclude current, top rated
        $similarBooks = Buku::withRating()
            ->where('buku.genre_buku', $book->genre_buku)
            ->where('buku.id', '!=', $id)
            ->orderByDesc('avg_rating')
            ->limit(8)
            ->get();

        return view('books.detail', compact('book', 'reviews', 'userRating', 'shelfEntry', 'similarBooks'));
    }

    public function rate(Request $request)
    {
        $bookId = (int) $request->buku_id;
        $rating = (int) $request->skor_rating;
        $review = trim($request->ulasan_teks ?? '');

        if ($bookId <= 0 || $rating < 1 || $rating > 5) {
            return redirect("/books/{$bookId}")->with('error', 'Data rating tidak valid.');
        }

        $book = Buku::find($bookId);
        if (!$book) {
            return redirect('/books')->with('error', 'Buku tidak ditemukan.');
        }

        RatingBuku::updateOrCreate(
            ['user_id' => auth()->id(), 'buku_id' => $bookId],
            ['skor_rating' => $rating, 'ulasan_teks' => $review ?: null]
        );

        if ($rating >= 4) {
            $this->recService->logBehavior(auth()->id(), 'beri_rating_tinggi', 5, $book->genre_buku, $book->judul);
        }

        \App\Models\LogAktivitas::record(auth()->id(), 'Rating Buku', "Memberikan rating {$rating} bintang untuk buku '{$book->judul}'");

        return redirect("/books/{$bookId}")->with('success', 'Ulasan berhasil disimpan!');
    }

    public function addToShelf(Request $request)
    {
        $bookId = (int) $request->buku_id;
        $status = $request->status ?? 'wishlist';

        $validStatuses = ['sedang_dibaca', 'selesai', 'wishlist'];
        if (!in_array($status, $validStatuses, true)) {
            $status = 'wishlist';
        }

        $book = Buku::find($bookId);
        if (!$book) {
            return redirect('/books')->with('error', 'Buku tidak ditemukan.');
        }

        RakBukuUser::updateOrCreate(
            ['user_id' => auth()->id(), 'buku_id' => $bookId],
            ['status' => $status]
        );

        if ($status === 'wishlist') {
            $this->recService->logBehavior(auth()->id(), 'tambah_wishlist', 5, $book->genre_buku, $book->judul);
        }

        \App\Models\LogAktivitas::record(auth()->id(), 'Rak Buku', "Menambahkan buku '{$book->judul}' ke rak ({$status})");

        return redirect("/books/{$bookId}")->with('success', 'Buku ditambahkan ke rak!');
    }

    public function removeFromShelf(Request $request)
    {
        $bookId = (int) $request->buku_id;
        $book = Buku::find($bookId);
        RakBukuUser::where('user_id', auth()->id())->where('buku_id', $bookId)->delete();

        if ($book) {
            \App\Models\LogAktivitas::record(auth()->id(), 'Rak Buku', "Menghapus buku '{$book->judul}' dari rak");
        }

        $redirect = $request->redirect ?? '/profile';
        return redirect($redirect);
    }

    public function search(Request $request)
    {
        $keyword = trim($request->get('search', $request->get('q', '')));
        $books = collect();

        if (!empty($keyword)) {
            $books = Buku::search($keyword)->get();

            if (auth()->check()) {
                $this->recService->logBehavior(auth()->id(), 'cari', 1, null, $keyword);
            }
        }

        return view('books.search', compact('books', 'keyword'));
    }
}
