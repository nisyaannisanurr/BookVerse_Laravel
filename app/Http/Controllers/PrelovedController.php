<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\PrelovedBook;
use App\Helpers\BookVerseHelper;
use App\Services\FileUploadService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class PrelovedController extends Controller
{
    protected FileUploadService $uploadService;
    protected RecommendationService $recService;

    public function __construct(FileUploadService $uploadService, RecommendationService $recService)
    {
        $this->uploadService = $uploadService;
        $this->recService = $recService;
    }

    public function index(Request $request)
    {
        $filters = [
            'kondisi'   => $request->get('kondisi', ''),
            'min_harga' => preg_replace('/[^0-9]/', '', $request->get('min_harga', '')),
            'max_harga' => preg_replace('/[^0-9]/', '', $request->get('max_harga', '')),
            'search'    => $request->get('search', ''),
        ];

        $listings = PrelovedBook::tersedia()
            ->filtered($filters)
            ->with('user:id,username,foto_profil')
            ->orderByDesc('created_at')
            ->get();

        return view('preloved.index', compact('listings', 'filters'));
    }

    public function detail(int $id)
    {
        $listing = PrelovedBook::with('user:id,username,foto_profil')->find($id);
        if (!$listing) abort(404);

        $waLink = BookVerseHelper::generateWhatsAppLink($listing->no_wa, $listing->judul_buku);

        // Log perilaku: coba cari genre dari katalog buku yang cocok judulnya
        if (auth()->check()) {
            $genre = null;
            // Coba match judul preloved ke buku di katalog (LIKE search)
            $matchedBook = Buku::where('judul', 'LIKE', '%' . trim(strtolower($listing->judul_buku)) . '%')
                ->orWhere(function($q) use ($listing) {
                    // Try first 3 words of title
                    $words = implode(' ', array_slice(explode(' ', $listing->judul_buku), 0, 3));
                    if (strlen($words) > 3) $q->where('judul', 'LIKE', "%{$words}%");
                })
                ->first();
            $genre = $matchedBook?->genre_buku;

            // Log sebagai klik_preloved (bobot 3 — antara klik biasa dan wishlist)
            $this->recService->logBehavior(
                auth()->id(),
                'klik_preloved',
                3,
                $genre,
                $listing->judul_buku
            );
        }

        return view('preloved.detail', compact('listing', 'waLink'));
    }

    public function showForm()
    {
        $listing = null;
        return view('preloved.form', compact('listing'));
    }

    public function store(Request $request)
    {
        $data = [
            'judul_buku'   => trim($request->judul_buku ?? ''),
            'kondisi_buku' => trim($request->kondisi_buku ?? ''),
            'harga'        => (float)($request->harga ?? 0),
            'deskripsi'    => trim($request->deskripsi ?? ''),
            'no_wa'        => trim($request->no_wa ?? ''),
        ];

        if (empty($data['judul_buku']) || empty($data['kondisi_buku']) || empty($data['deskripsi']) || empty($data['no_wa'])) {
            return redirect('/preloved/create')->with('error', 'Semua field harus diisi.');
        }

        if (!$request->hasFile('foto_buku')) {
            return redirect('/preloved/create')->with('error', 'Foto buku harus diunggah.');
        }

        $photo = $this->uploadService->uploadImage($request->file('foto_buku'), 'preloved');
        if (!$photo) {
            return redirect('/preloved/create')->with('error', 'Gagal mengunggah foto. Pastikan file adalah gambar (JPG/PNG/WEBP) dan ukuran maksimal 5MB.');
        }

        $data['user_id'] = auth()->id();
        $data['foto_buku'] = $photo;

        $preloved = PrelovedBook::create($data);

        \App\Models\LogAktivitas::record(auth()->id(), 'Create Preloved', "Membuat listing preloved baru: {$preloved->judul_buku}");

        return redirect('/preloved')->with('success', 'Buku berhasil diposting!');
    }

    public function showEditForm(int $id)
    {
        $listing = PrelovedBook::find($id);
        if (!$listing || $listing->user_id !== auth()->id()) abort(403);

        return view('preloved.form', compact('listing'));
    }

    public function update(Request $request, int $id)
    {
        $listing = PrelovedBook::find($id);
        if (!$listing || $listing->user_id !== auth()->id()) abort(403);

        $data = [
            'judul_buku'   => trim($request->judul_buku ?? ''),
            'kondisi_buku' => trim($request->kondisi_buku ?? ''),
            'harga'        => (float)($request->harga ?? 0),
            'deskripsi'    => trim($request->deskripsi ?? ''),
            'no_wa'        => trim($request->no_wa ?? ''),
        ];

        if ($request->hasFile('foto_buku')) {
            $photo = $this->uploadService->uploadImage($request->file('foto_buku'), 'preloved');
            if ($photo) {
                $this->uploadService->deleteUpload('preloved', $listing->foto_buku);
                $data['foto_buku'] = $photo;
            }
        }

        $listing->update($data);

        \App\Models\LogAktivitas::record(auth()->id(), 'Update Preloved', "Memperbarui data buku preloved '{$listing->judul_buku}'");

        return redirect("/preloved/{$id}")->with('success', 'Buku berhasil diperbarui!');
    }

    public function delete(Request $request)
    {
        $id = (int) $request->listing_id;
        $listing = PrelovedBook::find($id);

        if (!$listing || ($listing->user_id !== auth()->id() && !auth()->user()->isSuperadmin())) {
            abort(403);
        }

        $this->uploadService->deleteUpload('preloved', $listing->foto_buku);
        $listingTitle = $listing->judul_buku;
        $listing->delete();

        \App\Models\LogAktivitas::record(auth()->id(), 'Delete Preloved', "Menghapus listing buku preloved '{$listingTitle}'");

        return redirect('/preloved')->with('success', 'Buku berhasil dihapus.');
    }

    public function markSold(Request $request)
    {
        $id = (int) $request->listing_id;
        $listing = PrelovedBook::find($id);

        if (!$listing || $listing->user_id !== auth()->id()) abort(403);

        $listing->update(['status_buku' => 'terjual']);

        \App\Models\LogAktivitas::record(auth()->id(), 'Preloved Terjual', "Menandai buku preloved '{$listing->judul_buku}' sebagai terjual");

        return redirect('/profile')->with('success', 'Status buku diperbarui menjadi terjual.');
    }
}
