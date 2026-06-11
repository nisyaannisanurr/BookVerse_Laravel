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

    public function submitReport(Request $request)
    {
        if (!auth()->check()) {
            return back()->with('error', 'Anda harus login untuk membuat laporan.');
        }

        $request->validate([
            'tipe_entitas' => 'required|in:buku,preloved,user,komunitas',
            'entitas_id'   => 'required|integer',
            'alasan'       => 'required|string|max:255',
            'detail_tambahan' => 'nullable|string|max:1000',
        ]);

        \App\Models\LaporanGlobal::create([
            'pelapor_id'   => auth()->id(),
            'tipe_entitas' => $request->tipe_entitas,
            'entitas_id'   => $request->entitas_id,
            'alasan'       => $request->alasan,
            'detail_tambahan' => $request->detail_tambahan,
            'status'       => 'pending',
        ]);

        \App\Models\LogAktivitas::record(auth()->id(), 'Buat Laporan', "Melaporkan entitas tipe '{$request->tipe_entitas}' dengan ID {$request->entitas_id}");

        return back()->with('success', 'Laporan Anda telah dikirim ke Superadmin. Terima kasih!');
    }
}
