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
use App\Models\LogAktivitas;
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
            'pending_mitra'     => \App\Models\MitraVerification::where('status_verifikasi', 'pending')->count(),
        ];

        // Chart Data: 6 Months Growth
        $chartData = [
            'labels' => [],
            'users' => [],
            'donations' => []
        ];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartData['labels'][] = $month->format('M Y');
            
            $chartData['users'][] = User::whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->count();
                                        
            $chartData['donations'][] = \App\Models\DonasiBuku::where('status_pengiriman', 'diterima')
                                        ->whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->sum('jumlah');
        }

        return view('admin.superadmin.dashboard', compact('stats', 'chartData'));
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
                'catatan_admin' => $request->catatan_admin ?? null
            ]);

            if ($request->status === 'selesai') {
                if ($report->tipe_entitas === 'mitra') {
                    $mitraId = $report->entitas_id;
                    
                    // Demote user role back to 3 (User)
                    $user = \App\Models\User::find($mitraId);
                    if ($user && $user->role_id == 2) {
                        $user->update(['role_id' => 3]);
                    }
    
                    // Reject MitraVerification
                    $mitraVer = \App\Models\MitraVerification::where('user_id', $mitraId)->first();
                    if ($mitraVer) {
                        $mitraVer->update([
                            'status_verifikasi' => 'rejected',
                            'catatan_admin' => 'Akun dinonaktifkan karena pelanggaran / laporan penipuan.'
                        ]);
                    }
    
                    // Optional: Close all active campaigns
                    \App\Models\CampaignDonasi::where('user_id', $mitraId)
                        ->where('status', 'active')
                        ->update(['status' => 'cancelled']);
    
                    \App\Models\Notifikasi::create([
                        'user_id'    => $mitraId,
                        'tipe'       => 'mitra_banned',
                        'pesan'      => 'Akun Mitra Anda telah dinonaktifkan karena melanggar ketentuan atau terindikasi penipuan.',
                        'url_target' => null,
                    ]);
                } elseif ($report->tipe_entitas === 'komunitas') {
                    $komunitas = \App\Models\Komunitas::find($report->entitas_id);
                    if ($komunitas) $komunitas->update(['status' => 'nonaktif']);
                } elseif ($report->tipe_entitas === 'preloved') {
                    $preloved = \App\Models\PrelovedBook::find($report->entitas_id);
                    if ($preloved) $preloved->update(['status_buku' => 'ditangguhkan']);
                } elseif ($report->tipe_entitas === 'user') {
                    $user = \App\Models\User::find($report->entitas_id);
                    if ($user) $user->update(['status_akun' => 'suspended']);
                } elseif ($report->tipe_entitas === 'postingan') {
                    $postingan = \App\Models\PostinganKomunitas::find($report->entitas_id);
                    if ($postingan) $postingan->delete();
                }
            }

            \App\Models\LogAktivitas::record(auth()->id(), 'Resolve Report', "Mengubah status laporan ID {$report->id} menjadi {$request->status}");
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

    public function detailUser($id)
    {
        $user = User::with(['prelovedBooks', 'komunitas'])->findOrFail($id);
        $totalDonasi = \App\Models\DonasiBuku::where('user_id', $id)->sum('jumlah') ?? 0;
        return view('admin.superadmin.detail_user', compact('user', 'totalDonasi'));
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
            'role_id'  => 'required|in:2,3,4',
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
        
        $chartData = [
            'labels' => [],
            'communities' => [],
            'members' => [],
            'posts' => []
        ];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartData['labels'][] = $month->format('M Y');
            
            $chartData['communities'][] = Komunitas::whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->count();
                                        
            $chartData['members'][] = \App\Models\AnggotaKomunitas::whereYear('tanggal_bergabung', $month->year)
                                        ->whereMonth('tanggal_bergabung', $month->month)
                                        ->count();
                                        
            $chartData['posts'][] = \App\Models\PostinganKomunitas::whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->count();
        }
        
        return view('admin.superadmin.communities', compact('communities', 'chartData'));
    }

    public function detailCommunity($id)
    {
        $community = Komunitas::with(['creator', 'anggota.user', 'postingan.user'])->findOrFail($id);
        
        $chartData = [
            'labels' => [],
            'members' => [],
            'posts' => []
        ];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartData['labels'][] = $month->format('M Y');
            
            $chartData['members'][] = \App\Models\AnggotaKomunitas::where('komunitas_id', $id)
                                        ->whereYear('tanggal_bergabung', $month->year)
                                        ->whereMonth('tanggal_bergabung', $month->month)
                                        ->count();
                                        
            $chartData['posts'][] = \App\Models\PostinganKomunitas::where('komunitas_id', $id)
                                        ->whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->count();
        }

        return view('admin.superadmin.detail_community', compact('community', 'chartData'));
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

        // Upgrade creator ke Admin Komunitas (role 2) jika masih User biasa (role 3)
        // Ini dilakukan di sini (bukan saat create) sesuai prinsip Least Privilege
        if ($community->creator_id) {
            $creator = User::find($community->creator_id);
            if ($creator && $creator->role_id === 3) {
                $creator->update(['role_id' => 2]);
            }
        }

        Notifikasi::create([
            'user_id'    => $community->creator_id,
            'tipe'       => 'komunitas_disetujui',
            'pesan'      => 'Komunitas "' . $community->nama_komunitas . '" telah disetujui! Anda sekarang menjadi Admin Komunitas.',
            'url_target' => "/community/{$id}/feed",
        ]);

        LogAktivitas::record(auth()->id(), 'Approve Komunitas', "Menyetujui komunitas: {$community->nama_komunitas}");

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

    public function detailPreloved($id)
    {
        $book = PrelovedBook::with('penjual')->findOrFail($id);
        return view('admin.superadmin.detail_preloved', compact('book'));
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

    // ─── INSTANSI DAERAH (WILAYAH MITRA) ───────────────────
    public function instansiDaerah()
    {
        $daerahs = \App\Models\InstansiDaerah::withCount('mitraVerifications')->orderBy('nama_daerah')->get();
        return view('admin.superadmin.instansi-daerah', compact('daerahs'));
    }

    public function createInstansiDaerah(Request $request)
    {
        $request->validate(['nama_daerah' => 'required|string|max:255|unique:instansi_daerahs']);
        \App\Models\InstansiDaerah::create(['nama_daerah' => $request->nama_daerah, 'is_active' => true]);
        return back()->with('success', 'Instansi daerah berhasil ditambahkan.');
    }

    public function editInstansiDaerah(Request $request, int $id)
    {
        $daerah = \App\Models\InstansiDaerah::find($id);
        if (!$daerah) return back()->with('error', 'Instansi daerah tidak ditemukan.');

        $request->validate(['nama_daerah' => 'required|string|max:255|unique:instansi_daerahs,nama_daerah,' . $id]);
        $daerah->update([
            'nama_daerah' => $request->nama_daerah,
            'is_active'   => $request->has('is_active') ? true : false,
        ]);
        return back()->with('success', 'Instansi daerah berhasil diperbarui.');
    }

    public function deleteInstansiDaerah(Request $request)
    {
        $daerah = \App\Models\InstansiDaerah::find($request->daerah_id);
        if (!$daerah) return back()->with('error', 'Instansi daerah tidak ditemukan.');
        if ($daerah->mitraVerifications()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus daerah yang sudah memiliki mitra. Silakan nonaktifkan saja.');
        }
        $daerah->delete();
        return back()->with('success', 'Instansi daerah berhasil dihapus.');
    }

    // ─── KELOLA MITRA ─────────────────────────────────
    public function verifikasiMitra()
    {
        $pendingVerifications = \App\Models\MitraVerification::with(['user', 'instansiDaerah'])
            ->where('status_verifikasi', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $allMitra = \App\Models\MitraVerification::with(['user', 'instansiDaerah'])
            ->orderByRaw("FIELD(status_verifikasi, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at')
            ->get();

        $pendingCampaigns = \App\Models\CampaignDonasi::where('status', 'pending')->with('user.mitraVerification')->get();

        return view('admin.superadmin.verifikasi-mitra', compact('pendingVerifications', 'allMitra', 'pendingCampaigns'));
    }

    public function detailMitra($id)
    {
        $mitra = \App\Models\MitraVerification::with(['user.campaigns', 'instansiDaerah'])->findOrFail($id);
        
        $campaigns = $mitra->user->campaigns()->orderByDesc('created_at')->get();

        return view('admin.superadmin.detail_mitra', compact('mitra', 'campaigns'));
    }

    public function detailCampaign($id)
    {
        $campaign = \App\Models\CampaignDonasi::with(['user.mitraVerification', 'donasiBuku.user'])->findOrFail($id);
        
        // Mengambil daftar donasi yang sudah selesai atau sedang proses
        $donations = $campaign->donasiBuku()->orderByDesc('created_at')->get();

        return view('admin.superadmin.detail_campaign', compact('campaign', 'donations'));
    }

    public function approveMitra(Request $request)
    {
        $id = (int) $request->verification_id;
        $verification = \App\Models\MitraVerification::find($id);
        if (!$verification) return back()->with('error', 'Data verifikasi tidak ditemukan.');

        $verification->update(['status_verifikasi' => 'approved', 'catatan_admin' => $request->catatan_admin]);

        Notifikasi::create([
            'user_id'    => $verification->user_id,
            'tipe'       => 'mitra_disetujui',
            'pesan'      => 'Selamat! Akun mitra "' . $verification->nama_instansi . '" telah diverifikasi. Anda sekarang bisa membuat kampanye donasi buku!',
            'url_target' => '/donasi/dashboard',
        ]);

        LogAktivitas::record(auth()->id(), 'Approve Mitra', "Menyetujui mitra: {$verification->nama_instansi}");

        return back()->with('success', 'Mitra "' . $verification->nama_instansi . '" berhasil disetujui!');
    }

    public function rejectMitra(Request $request)
    {
        $id = (int) $request->verification_id;
        $catatan = trim($request->catatan_admin ?? '');
        $verification = \App\Models\MitraVerification::find($id);
        if (!$verification) return back()->with('error', 'Data verifikasi tidak ditemukan.');

        $verification->update(['status_verifikasi' => 'rejected', 'catatan_admin' => $catatan ?: null]);

        Notifikasi::create([
            'user_id'    => $verification->user_id,
            'tipe'       => 'mitra_ditolak',
            'pesan'      => 'Pendaftaran mitra "' . $verification->nama_instansi . '" ditolak.' . ($catatan ? " Alasan: {$catatan}" : ''),
            'url_target' => null,
        ]);

        LogAktivitas::record(auth()->id(), 'Reject Mitra', "Menolak mitra: {$verification->nama_instansi}");

        return back()->with('success', 'Mitra ditolak.');
    }

    public function suspendMitra(Request $request)
    {
        $id = (int) $request->verification_id;
        $verification = \App\Models\MitraVerification::find($id);
        if (!$verification) return back()->with('error', 'Data mitra tidak ditemukan.');

        $statusBaru = $verification->status_verifikasi === 'approved' ? 'suspended' : 'approved';
        $verification->update(['status_verifikasi' => $statusBaru]);

        $pesanTeks = $statusBaru === 'suspended' ? 'dinonaktifkan' : 'diaktifkan kembali';
        $pesanAksi = $statusBaru === 'suspended' ? 'Menonaktifkan' : 'Mengaktifkan';

        Notifikasi::create([
            'user_id'    => $verification->user_id,
            'tipe'       => 'mitra_' . $statusBaru,
            'pesan'      => 'Akun mitra Anda ("' . $verification->nama_instansi . '") telah ' . $pesanTeks . ' oleh Superadmin.',
            'url_target' => '/donasi/dashboard',
        ]);

        LogAktivitas::record(auth()->id(), 'Suspend Mitra', "{$pesanAksi} mitra: {$verification->nama_instansi}");

        return back()->with('success', "Mitra berhasil {$pesanTeks}.");
    }

    public function deleteMitra(Request $request)
    {
        $id = (int) $request->verification_id;
        $verification = \App\Models\MitraVerification::find($id);
        if (!$verification) return back()->with('error', 'Data mitra tidak ditemukan.');

        $namaInstansi = $verification->nama_instansi;

        // Optionally, remove the user's role_id 4 back to 3 (User) or delete user
        if ($verification->user) {
            $verification->user->update(['role_id' => 3]);
        }

        $verification->delete();

        LogAktivitas::record(auth()->id(), 'Delete Mitra', "Menghapus mitra: {$namaInstansi}");

        return back()->with('success', 'Data mitra berhasil dihapus.');
    }

    public function approveCampaignDonasi(Request $request)
    {
        $id = (int) $request->campaign_id;
        $campaign = \App\Models\CampaignDonasi::find($id);
        if (!$campaign) return back()->with('error', 'Kampanye tidak ditemukan.');

        $campaign->update(['status' => 'active']);

        Notifikasi::create([
            'user_id'    => $campaign->user_id,
            'tipe'       => 'kampanye_disetujui',
            'pesan'      => 'Kampanye donasi "' . $campaign->judul . '" telah disetujui dan sekarang tampil di halaman publik!',
            'url_target' => '/donasi/' . $campaign->id,
        ]);

        return back()->with('success', 'Kampanye berhasil disetujui!');
    }

    public function rejectCampaignDonasi(Request $request)
    {
        $id = (int) $request->campaign_id;
        $catatan = trim($request->catatan_admin ?? '');
        $campaign = \App\Models\CampaignDonasi::find($id);
        if (!$campaign) return back()->with('error', 'Kampanye tidak ditemukan.');

        $campaign->update(['status' => 'rejected', 'catatan_admin' => $catatan ?: null]);

        Notifikasi::create([
            'user_id'    => $campaign->user_id,
            'tipe'       => 'kampanye_ditolak',
            'pesan'      => 'Kampanye donasi "' . $campaign->judul . '" ditolak.' . ($catatan ? " Alasan: {$catatan}" : ''),
            'url_target' => null,
        ]);

        return back()->with('success', 'Kampanye ditolak.');
    }


    // ─── PANDUAN (FAQ) ───────────────────────────────────────
    public function panduans()
    {
        $panduans = \App\Models\Panduan::orderBy('kategori')->orderBy('urutan')->get();
        $kategoriList = \App\Models\KategoriPanduan::orderBy('nama')->get();
        return view('admin.superadmin.panduan', compact('panduans', 'kategoriList'));
    }

    public function createPanduan(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban'    => 'required|string',
            'kategori'   => 'required|string|max:100',
            'urutan'     => 'nullable|integer'
        ]);

        \App\Models\Panduan::create([
            'pertanyaan' => $request->pertanyaan,
            'jawaban'    => $request->jawaban,
            'kategori'   => $request->kategori,
            'urutan'     => $request->urutan ?? 0
        ]);

        return redirect()->back()->with('success', 'Panduan berhasil ditambahkan.');
    }

    public function editPanduan(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban'    => 'required|string',
            'kategori'   => 'required|string|max:100',
            'urutan'     => 'nullable|integer'
        ]);

        $panduan = \App\Models\Panduan::findOrFail($id);
        $panduan->update([
            'pertanyaan' => $request->pertanyaan,
            'jawaban'    => $request->jawaban,
            'kategori'   => $request->kategori,
            'urutan'     => $request->urutan ?? 0
        ]);

        return redirect()->back()->with('success', 'Panduan berhasil diperbarui.');
    }

    public function deletePanduan(Request $request)
    {
        $panduan = \App\Models\Panduan::findOrFail($request->id);
        $panduan->delete();

        return redirect()->back()->with('success', 'Panduan berhasil dihapus.');
    }

    // ─── KATEGORI PANDUAN ───────────────────────────────────────
    public function kategoriPanduans()
    {
        $kategories = \App\Models\KategoriPanduan::orderBy('nama')->get();
        return view('admin.superadmin.kategori-panduan', compact('kategories'));
    }

    public function createKategoriPanduan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_panduans,nama'
        ]);

        \App\Models\KategoriPanduan::create(['nama' => $request->nama]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategoriPanduan(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_panduans,nama,' . $id
        ]);

        $kategori = \App\Models\KategoriPanduan::findOrFail($id);
        
        // Update kategori lama di panduans table
        $oldNama = $kategori->nama;
        \App\Models\Panduan::where('kategori', $oldNama)->update(['kategori' => $request->nama]);
        
        $kategori->update(['nama' => $request->nama]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function deleteKategoriPanduan(Request $request)
    {
        $kategori = \App\Models\KategoriPanduan::findOrFail($request->id);
        
        // Cek apakah ada panduan dengan kategori ini
        $count = \App\Models\Panduan::where('kategori', $kategori->nama)->count();
        if ($count > 0) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $count . ' panduan. Silakan ubah atau hapus panduan tersebut terlebih dahulu.');
        }

        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }
}
