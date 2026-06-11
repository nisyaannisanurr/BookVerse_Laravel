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

        $book->delete();

        return redirect('/admin/superadmin/books')->with('success', 'Buku berhasil dihapus (masuk ke Tong Sampah).');
    }

    // ─── TONG SAMPAH (TRASH) ───────────────────────────
    public function trash()
    {
        $books = Buku::onlyTrashed()->orderByDesc('deleted_at')->get();
        $posts = \App\Models\PostinganKomunitas::onlyTrashed()->with(['user', 'komunitas'])->orderByDesc('deleted_at')->get();
        return view('admin.superadmin.trash', compact('books', 'posts'));
    }

    public function restoreBook(Request $request)
    {
        $id = (int) $request->book_id;
        $book = Buku::onlyTrashed()->find($id);
        if ($book) {
            $book->restore();
            return back()->with('success', 'Buku berhasil dipulihkan.');
        }
        return back()->with('error', 'Buku tidak ditemukan di Tong Sampah.');
    }

    public function forceDeleteBook(Request $request)
    {
        $id = (int) $request->book_id;
        $book = Buku::onlyTrashed()->find($id);
        if ($book) {
            $this->uploadService->deleteUpload('covers', $book->cover_buku);
            $book->forceDelete();
            return back()->with('success', 'Buku dihapus permanen beserta filenya.');
        }
        return back()->with('error', 'Buku tidak ditemukan di Tong Sampah.');
    }

    public function restorePost(Request $request)
    {
        $id = (int) $request->post_id;
        $post = \App\Models\PostinganKomunitas::onlyTrashed()->find($id);
        if ($post) {
            $post->restore();
            return back()->with('success', 'Postingan komunitas berhasil dipulihkan.');
        }
        return back()->with('error', 'Postingan tidak ditemukan di Tong Sampah.');
    }

    public function forceDeletePost(Request $request)
    {
        $id = (int) $request->post_id;
        $post = \App\Models\PostinganKomunitas::onlyTrashed()->find($id);
        if ($post) {
            if ($post->gambar) {
                $gambarPaths = json_decode($post->gambar, true);
                if (is_array($gambarPaths)) {
                    foreach ($gambarPaths as $path) {
                        $this->uploadService->deleteUpload('', $path); // path include 'post_images/'
                    }
                }
            }
            $post->forceDelete();
            return back()->with('success', 'Postingan dihapus permanen.');
        }
        return back()->with('error', 'Postingan tidak ditemukan di Tong Sampah.');
    }

    // ─── PUSAT LAPORAN GLOBAL ──────────────────────────
    public function reports()
    {
        $reports = \App\Models\LaporanGlobal::with('pelapor')
            ->orderByRaw("FIELD(status, 'pending', 'diproses', 'selesai', 'ditolak')")
            ->orderByDesc('created_at')
            ->get();
        return view('admin.superadmin.reports', compact('reports'));
    }

    public function resolveReport(Request $request)
    {
        $id = (int) $request->report_id;
        $report = \App\Models\LaporanGlobal::find($id);
        if ($report) {
            $report->update([
                'status' => $request->status,
                'catatan_admin' => $request->catatan_admin
            ]);
            return back()->with('success', 'Status laporan berhasil diperbarui.');
        }
        return back()->with('error', 'Laporan tidak ditemukan.');
    }

    // ─── BROADCAST NOTIFIKASI ──────────────────────────
    public function broadcastForm()
    {
        return view('admin.superadmin.broadcast');
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'tipe' => 'required|string|max:50',
            'pesan' => 'required|string|max:500',
            'url_target' => 'nullable|url',
        ]);

        $users = User::all();
        $notifData = [];
        $now = now();

        foreach ($users as $u) {
            $notifData[] = [
                'user_id' => $u->id,
                'tipe' => $request->tipe,
                'pesan' => $request->pesan,
                'url_target' => $request->url_target,
                'is_read' => 0,
                'created_at' => $now
            ];
        }

        // Chunk insert to handle large amount of users
        foreach (array_chunk($notifData, 500) as $chunk) {
            \App\Models\Notifikasi::insert($chunk);
        }

        return back()->with('success', 'Notifikasi broadcast berhasil dikirim ke ' . count($users) . ' pengguna.');
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

    public function suspendUser(Request $request)
    {
        $id = (int) $request->user_id;
        $user = User::find($id);
        if (!$user || $user->isSuperadmin()) {
            return redirect('/admin/superadmin/users')->with('error', 'User tidak ditemukan atau tidak bisa disuspend.');
        }

        $newStatus = $user->status_akun === 'aktif' ? 'suspended' : 'aktif';
        $user->update(['status_akun' => $newStatus]);

        return redirect('/admin/superadmin/users')->with('success', 'Status user berhasil diperbarui.');
    }

    public function editUserForm(int $id)
    {
        $user = User::find($id);
        if (!$user || $user->isSuperadmin()) {
            return redirect('/admin/superadmin/users')->with('error', 'User tidak ditemukan atau tidak bisa diedit.');
        }

        $roles = \Illuminate\Support\Facades\DB::table('roles')->where('id', '!=', 1)->get();
        return view('admin.superadmin.user_edit', compact('user', 'roles'));
    }

    public function editUser(Request $request, int $id)
    {
        $user = User::find($id);
        if (!$user || $user->isSuperadmin()) {
            return redirect('/admin/superadmin/users')->with('error', 'User tidak ditemukan atau tidak bisa diedit.');
        }

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'role_id'  => 'required|in:2,3',
        ]);

        $data = [
            'username' => $request->username,
            'bio'      => $request->bio,
            'role_id'  => $request->role_id,
        ];

        if ($request->has('remove_foto')) {
            if ($user->foto_profil) {
                $this->uploadService->deleteUpload('profiles', $user->foto_profil);
                $data['foto_profil'] = null;
            }
        }

        $user->update($data);

        return redirect('/admin/superadmin/users')->with('success', 'Data user berhasil diperbarui.');
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

    // ─── PENGATURAN SISTEM (SETTINGS) ──────────────────────
    public function settings()
    {
        $maintenance_mode = \App\Models\SystemSetting::getSetting('maintenance_mode', 'false');
        $preloved_enabled = \App\Models\SystemSetting::getSetting('preloved_enabled', 'true');
        
        return view('admin.superadmin.settings', compact('maintenance_mode', 'preloved_enabled'));
    }

    public function updateSettings(Request $request)
    {
        $maintenance = $request->has('maintenance_mode') ? 'true' : 'false';
        $preloved = $request->has('preloved_enabled') ? 'true' : 'false';

        \App\Models\SystemSetting::setSetting('maintenance_mode', $maintenance);
        \App\Models\SystemSetting::setSetting('preloved_enabled', $preloved);

        \App\Models\LogAktivitas::record(auth()->id(), 'Ubah Pengaturan', "Memperbarui pengaturan sistem (Maintenance: {$maintenance})");

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    // ─── KAMUS KATA KASAR (PROFANITY FILTER) ───────────────
    public function profanities()
    {
        $words = \App\Models\Profanity::orderByDesc('id')->paginate(30);
        return view('admin.superadmin.profanity', compact('words'));
    }

    public function addProfanity(Request $request)
    {
        $request->validate(['kata' => 'required|string|max:50|unique:profanities,kata']);
        
        \App\Models\Profanity::create(['kata' => strtolower($request->kata)]);
        \Illuminate\Support\Facades\Cache::forget('profanity_words');

        \App\Models\LogAktivitas::record(auth()->id(), 'Tambah Kata Kasar', "Menambahkan kata '{$request->kata}' ke filter");

        return back()->with('success', 'Kata berhasil ditambahkan ke filter.');
    }

    public function deleteProfanity(Request $request)
    {
        $id = (int) $request->word_id;
        $word = \App\Models\Profanity::find($id);
        if ($word) {
            $wordStr = $word->kata;
            $word->delete();
            \Illuminate\Support\Facades\Cache::forget('profanity_words');
            
            \App\Models\LogAktivitas::record(auth()->id(), 'Hapus Kata Kasar', "Menghapus kata '{$wordStr}' dari filter");
            return back()->with('success', 'Kata berhasil dihapus dari filter.');
        }
        return back()->with('error', 'Kata tidak ditemukan.');
    }

    // ─── LOGS ──────────────────────────────────────────────
    public function logs()
    {
        $logs = \App\Models\LogAktivitas::with('user:id,username,email,foto_profil')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.superadmin.logs', compact('logs'));
    }
}
