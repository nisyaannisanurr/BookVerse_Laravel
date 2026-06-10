<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKomunitas;
use App\Models\Buku;
use App\Models\Genre;
use App\Models\Komunitas;
use App\Models\Notifikasi;
use App\Models\PrelovedBook;
use App\Models\RatingBuku;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class SuperadminController extends Controller
{
    protected FileUploadService $uploadService;

    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function dashboard()
    {
        $stats = [
            'total_users'       => User::nonSuperadmin()->count(),
            'total_books'       => Buku::count(),
            'total_communities' => Komunitas::aktif()->count(),
            'pending_communities' => Komunitas::where('status', 'pending')->count(),
            'total_preloved'    => PrelovedBook::count(),
            'total_ratings'     => RatingBuku::count(),
        ];

        return view('admin.superadmin.dashboard', compact('stats'));
    }

    // ─── BOOKS ─────────────────────────────────────────
    public function books()
    {
        $books  = Buku::withRating()->orderByDesc('buku.created_at')->get();
        $genres = Genre::getAllOrdered();
        return view('admin.superadmin.books', compact('books', 'genres'));
    }

    public function editBookForm(int $id)
    {
        $book   = Buku::find($id);
        if (!$book) return redirect('/admin/superadmin/books')->with('error', 'Buku tidak ditemukan.');
        $genres = Genre::getAllOrdered();
        return view('admin.superadmin.book_edit', compact('book', 'genres'));
    }

    public function createBook(Request $request)
    {
        $data = [
            'judul'      => trim($request->judul ?? ''),
            'penulis'    => trim($request->penulis ?? ''),
            'sinopsis'   => trim($request->sinopsis ?? ''),
            'genre_buku' => trim($request->genre_buku ?? ''),
        ];

        if (empty($data['judul']) || empty($data['penulis']) || empty($data['genre_buku'])) {
            return redirect('/admin/superadmin/books')->with('error', 'Judul, penulis, dan genre harus diisi.');
        }

        if ($request->hasFile('cover_buku')) {
            $cover = $this->uploadService->uploadImage($request->file('cover_buku'), 'covers');
            if ($cover) $data['cover_buku'] = $cover;
        }

        Buku::create($data);
        return redirect('/admin/superadmin/books')->with('success', 'Buku "'.$data['judul'].'" berhasil ditambahkan!');
    }

    public function editBook(Request $request, int $id)
    {
        $book = Buku::find($id);
        if (!$book) return redirect('/admin/superadmin/books')->with('error', 'Buku tidak ditemukan.');

        $data = [
            'judul'      => trim($request->judul ?? $book->judul),
            'penulis'    => trim($request->penulis ?? $book->penulis),
            'sinopsis'   => trim($request->sinopsis ?? $book->sinopsis),
            'genre_buku' => trim($request->genre_buku ?? $book->genre_buku),
        ];

        if ($request->hasFile('cover_buku')) {
            $cover = $this->uploadService->uploadImage($request->file('cover_buku'), 'covers');
            if ($cover) {
                $this->uploadService->deleteUpload('covers', $book->cover_buku);
                $data['cover_buku'] = $cover;
            }
        }

        $book->update($data);

        return redirect('/admin/superadmin/books')->with('success', 'Buku berhasil diperbarui!');
    }

    public function deleteBook(Request $request)
    {
        $id = (int) $request->book_id;
        $book = Buku::find($id);
        if (!$book) return redirect('/admin/superadmin/books')->with('error', 'Buku tidak ditemukan.');

        $this->uploadService->deleteUpload('covers', $book->cover_buku);
        $book->delete();

        return redirect('/admin/superadmin/books')->with('success', 'Buku berhasil dihapus.');
    }

    // ─── USERS ─────────────────────────────────────────
    public function users()
    {
        $users = User::nonSuperadmin()->with('role')->orderByDesc('created_at')->get();
        return view('admin.superadmin.users', compact('users'));
    }

    public function deleteUser(Request $request)
    {
        $id = (int) $request->user_id;
        $user = User::find($id);
        if (!$user || $user->isSuperadmin()) {
            return redirect('/admin/superadmin/users')->with('error', 'User tidak ditemukan atau tidak bisa dihapus.');
        }

        $user->delete();

        return redirect('/admin/superadmin/users')->with('success', 'User berhasil dihapus.');
    }

    // ─── COMMUNITIES ───────────────────────────────────
    public function communities()
    {
        $communities = Komunitas::withMemberCount()->orderByDesc('created_at')->get();
        return view('admin.superadmin.communities', compact('communities'));
    }

    public function approveCommunity(Request $request)
    {
        $id = (int) $request->community_id;
        $community = Komunitas::find($id);
        if (!$community) return redirect('/admin/superadmin/communities')->with('error', 'Komunitas tidak ditemukan.');

        $community->update(['status' => 'aktif']);

        // Auto-approve creator as member
        AnggotaKomunitas::updateOrCreate(
            ['user_id' => $community->creator_id, 'komunitas_id' => $id],
            ['status' => 'approved']
        );

        Notifikasi::create([
            'user_id'    => $community->creator_id,
            'tipe'       => 'komunitas_disetujui',
            'pesan'      => 'Komunitas "' . $community->nama_komunitas . '" telah disetujui!',
            'url_target' => "/community/{$id}/feed",
        ]);

        return redirect('/admin/superadmin/communities')->with('success', 'Komunitas berhasil disetujui!');
    }

    public function rejectCommunity(Request $request)
    {
        $id = (int) $request->community_id;
        $catatan = trim($request->catatan_penolakan ?? '');
        $community = Komunitas::find($id);
        if (!$community) return redirect('/admin/superadmin/communities')->with('error', 'Komunitas tidak ditemukan.');

        $community->update(['status' => 'nonaktif', 'catatan_penolakan' => $catatan ?: null]);

        Notifikasi::create([
            'user_id'    => $community->creator_id,
            'tipe'       => 'komunitas_ditolak',
            'pesan'      => 'Komunitas "' . $community->nama_komunitas . '" ditolak.' . ($catatan ? " Alasan: {$catatan}" : ''),
            'url_target' => null,
        ]);

        return redirect('/admin/superadmin/communities')->with('success', 'Komunitas ditolak.');
    }

    public function suspendCommunity(Request $request)
    {
        $id = (int) $request->community_id;
        $community = Komunitas::find($id);
        if (!$community) return redirect('/admin/superadmin/communities')->with('error', 'Komunitas tidak ditemukan.');

        $newStatus = $community->status === 'aktif' ? 'nonaktif' : 'aktif';
        $community->update(['status' => $newStatus]);

        return redirect('/admin/superadmin/communities')->with('success', 'Status komunitas diperbarui.');
    }

    // ─── PRELOVED ──────────────────────────────────────
    public function preloved()
    {
        $listings = PrelovedBook::with('user:id,username')->orderByDesc('created_at')->get();
        return view('admin.superadmin.preloved', compact('listings'));
    }

    public function suspendListing(Request $request)
    {
        $id = (int) $request->listing_id;
        $listing = PrelovedBook::find($id);
        if (!$listing) return redirect('/admin/superadmin/preloved')->with('error', 'Listing tidak ditemukan.');

        $newStatus = $listing->status_buku === 'ditangguhkan' ? 'tersedia' : 'ditangguhkan';
        $listing->update(['status_buku' => $newStatus]);

        if ($newStatus === 'ditangguhkan') {
            Notifikasi::create([
                'user_id'    => $listing->user_id,
                'tipe'       => 'preloved_ditangguhkan',
                'pesan'      => 'Listing buku "' . $listing->judul_buku . '" telah ditangguhkan oleh admin.',
                'url_target' => null,
            ]);
        }

        return redirect('/admin/superadmin/preloved')->with('success', 'Status listing diperbarui.');
    }

    public function deleteListing(Request $request)
    {
        $id = (int) $request->listing_id;
        $listing = PrelovedBook::find($id);
        if (!$listing) return redirect('/admin/superadmin/preloved')->with('error', 'Listing tidak ditemukan.');

        $this->uploadService->deleteUpload('preloved', $listing->foto_buku);
        $listing->delete();

        return redirect('/admin/superadmin/preloved')->with('success', 'Listing berhasil dihapus.');
    }

    // ─── GENRES ────────────────────────────────────────
    public function genres()
    {
        $genres = Genre::withCount(['bukus'])->orderBy('nama_genre')->get();
        return view('admin.superadmin.genres', compact('genres'));
    }

    public function createGenre(Request $request)
    {
        $nama = trim($request->nama_genre ?? '');
        if (empty($nama)) {
            return redirect('/admin/superadmin/genres')->with('error', 'Nama genre harus diisi.');
        }
        if (Genre::where('nama_genre', $nama)->exists()) {
            return redirect('/admin/superadmin/genres')->with('error', "Genre '$nama' sudah ada.");
        }
        Genre::create([
            'nama_genre' => $nama,
            'deskripsi'  => trim($request->deskripsi ?? ''),
            'warna'      => $request->warna ?? '#4f3cc9',
        ]);
        return redirect('/admin/superadmin/genres')->with('success', "Genre '$nama' berhasil ditambahkan!");
    }

    public function editGenre(Request $request, int $id)
    {
        $genre = Genre::find($id);
        if (!$genre) return redirect('/admin/superadmin/genres')->with('error', 'Genre tidak ditemukan.');

        $namaBaru = trim($request->nama_genre ?? $genre->nama_genre);
        $namaLama = $genre->nama_genre;

        $genre->update([
            'nama_genre' => $namaBaru,
            'deskripsi'  => trim($request->deskripsi ?? $genre->deskripsi ?? ''),
            'warna'      => $request->warna ?? $genre->warna,
        ]);

        // Sync nama genre di tabel buku jika nama berubah
        if ($namaBaru !== $namaLama) {
            Buku::where('genre_buku', $namaLama)->update(['genre_buku' => $namaBaru]);
        }

        return redirect('/admin/superadmin/genres')->with('success', 'Genre berhasil diperbarui!');
    }

    public function deleteGenre(Request $request)
    {
        $id = (int) $request->genre_id;
        $genre = Genre::find($id);
        if (!$genre) return redirect('/admin/superadmin/genres')->with('error', 'Genre tidak ditemukan.');

        $count = Buku::where('genre_buku', $genre->nama_genre)->count();
        if ($count > 0) {
            return redirect('/admin/superadmin/genres')
                ->with('error', "Genre '{$genre->nama_genre}' tidak bisa dihapus karena masih digunakan oleh {$count} buku.");
        }

        $genre->delete();
        return redirect('/admin/superadmin/genres')->with('success', 'Genre berhasil dihapus.');
    }
}
