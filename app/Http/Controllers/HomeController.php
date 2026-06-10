<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Komunitas;
use App\Models\RatingBuku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $trendingBooks = Buku::getPopularThisWeek(5);

        $popularCommunities = Komunitas::where('status', 'aktif')
            ->withCount(['anggota as member_count' => fn($q) => $q->where('status', 'approved')])
            ->orderByDesc('member_count')
            ->limit(5)
            ->get();

        $featuredReviews = RatingBuku::with(['user', 'buku'])
            ->whereNotNull('ulasan_teks')
            ->where('ulasan_teks', '!=', '')
            ->where('skor_rating', '>=', 4)
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view('home.index', compact('trendingBooks', 'popularCommunities', 'featuredReviews'));
    }
}
