<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MitraAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PrelovedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Admin\SuperadminController;
use App\Http\Controllers\Admin\AdminKomunitasController;
use App\Http\Controllers\SuperadminAuthController;
use Illuminate\Support\Facades\Route;

// ─── HOME & MISC ───────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/panduan', [HomeController::class, 'panduan'])->name('panduan');
Route::post('/report', [HomeController::class, 'submitReport'])->name('report.submit');
Route::get('/search', [BookController::class, 'search'])->name('search');
Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
Route::get('/about', fn() => view('pages.about'))->name('about');

// ─── AUTH ROUTES ───────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); // fallback

// ─── GOOGLE OAUTH ──────────────────────────────────────────
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ─── SUPERADMIN DEDICATED LOGIN ────────────────────────────
Route::get('/superadmin/login', [SuperadminAuthController::class, 'showLogin'])->name('superadmin.login');
Route::post('/superadmin/login', [SuperadminAuthController::class, 'login'])->name('superadmin.login.post')->middleware('throttle:5,1');
Route::post('/superadmin/logout', [SuperadminAuthController::class, 'logout'])->name('superadmin.logout');
Route::get('/superadmin/verify-otp', [SuperadminAuthController::class, 'showVerifyOtp'])->name('superadmin.verify-otp');
Route::post('/superadmin/verify-otp', [SuperadminAuthController::class, 'verifyOtp'])->name('superadmin.verify-otp.post');

// ─── ADMIN KOMUNITAS DEDICATED AUTH ────────────────────────
Route::get('/komunitas/masuk',  [\App\Http\Controllers\AdminKomunitasAuthController::class, 'showLogin'])->name('komunitas.admin.login');
Route::post('/komunitas/masuk', [\App\Http\Controllers\AdminKomunitasAuthController::class, 'login'])->name('komunitas.admin.login.post')->middleware('throttle:5,1');
Route::get('/komunitas/daftar', [\App\Http\Controllers\AdminKomunitasAuthController::class, 'showRegister'])->name('komunitas.admin.register');
Route::post('/komunitas/daftar',[\App\Http\Controllers\AdminKomunitasAuthController::class, 'register'])->name('komunitas.admin.register.post')->middleware('throttle:5,1');
Route::post('/komunitas/keluar',[\App\Http\Controllers\AdminKomunitasAuthController::class, 'logout'])->name('komunitas.admin.logout');

// ─── MITRA AUTH ────────────────────────────────────────────
Route::get('/mitra/masuk',   [MitraAuthController::class, 'showLogin'])->name('mitra.login');
Route::post('/mitra/masuk',  [MitraAuthController::class, 'login'])->name('mitra.login.post')->middleware('throttle:5,1');
Route::get('/mitra/daftar',  [MitraAuthController::class, 'showRegister'])->name('mitra.register');
Route::post('/mitra/daftar', [MitraAuthController::class, 'register'])->name('mitra.register.post')->middleware('throttle:5,1');

// ─── BOOKS ─────────────────────────────────────────────────
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{id}', [BookController::class, 'detail'])->name('books.detail')->where('id', '[0-9]+');

Route::middleware('auth')->group(function () {
    Route::post('/books/rate', [BookController::class, 'rate'])->name('books.rate');
    Route::post('/books/shelf', [BookController::class, 'addToShelf'])->name('books.shelf.add');
    Route::post('/books/shelf/remove', [BookController::class, 'removeFromShelf'])->name('books.shelf.remove');
});

// ─── COMMUNITY ─────────────────────────────────────────────
Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
Route::get('/community/{id}', [CommunityController::class, 'gate'])->name('community.gate')->where('id', '[0-9]+');

Route::middleware('auth')->group(function () {
    Route::post('/community/{id}/join', [CommunityController::class, 'join'])->name('community.join');
    Route::post('/community/{id}/leave', [CommunityController::class, 'leave'])->name('community.leave');

    // Semua user bisa ajukan komunitas → otomatis jadi admin komunitas
    Route::get('/community/create', [CommunityController::class, 'showCreateForm'])->name('community.create');
    Route::post('/community/create', [CommunityController::class, 'create']);

    // Community feed, post & chat (requires community access)
    Route::get('/community/{id}/feed', [CommunityController::class, 'feed'])->name('community.feed');
    Route::get('/community/{id}/chat', [CommunityController::class, 'chatIndex'])->name('community.chat');
    Route::get('/community/{id}/chat/fetch', [CommunityController::class, 'fetchChats'])->name('community.chat.fetch');
    Route::post('/community/{id}/chat/send', [CommunityController::class, 'sendChat'])->name('community.chat.send')->middleware('throttle:15,1');
    Route::post('/community/{id}/chat/{chatId}/react', [CommunityController::class, 'reactChat'])->name('community.chat.react');
    Route::post('/community/{id}/post', [CommunityController::class, 'createPost'])->name('community.post.create')->middleware('throttle:15,1');
    Route::get('/community/post/{id}', [CommunityController::class, 'postDetail'])->name('community.post.detail');
    Route::post('/community/post/{id}/like', [CommunityController::class, 'likePost'])->name('community.post.like');
    Route::post('/community/comment', [CommunityController::class, 'addComment'])->name('community.comment.add')->middleware('throttle:15,1');
    Route::post('/community/post/delete', [CommunityController::class, 'deletePost'])->name('community.post.delete');
    Route::post('/community/comment/delete', [CommunityController::class, 'deleteComment'])->name('community.comment.delete');
    Route::post('/community/report', [CommunityController::class, 'report'])->name('community.report');
    Route::post('/community/{id}/challenge/join', [CommunityController::class, 'joinChallenge'])->name('community.challenge.join');
    Route::get('/community/{id}/challenge/{challengeId}', [CommunityController::class, 'showChallenge'])->name('community.challenge.show');
    Route::post('/community/{id}/qna/ask', [CommunityController::class, 'askQna'])->name('community.qna.ask')->middleware('throttle:15,1');
});

// ─── PRELOVED ──────────────────────────────────────────────
Route::get('/preloved', [PrelovedController::class, 'index'])->name('preloved.index');
Route::get('/preloved/{id}', [PrelovedController::class, 'detail'])->name('preloved.detail')->where('id', '[0-9]+');

Route::middleware('auth')->group(function () {
    Route::get('/preloved/create', [PrelovedController::class, 'showForm'])->name('preloved.create');
    Route::post('/preloved/create', [PrelovedController::class, 'store']);
    Route::get('/preloved/{id}/edit', [PrelovedController::class, 'showEditForm'])->name('preloved.edit');
    Route::post('/preloved/{id}/edit', [PrelovedController::class, 'update']);
    Route::post('/preloved/delete', [PrelovedController::class, 'delete'])->name('preloved.delete');
    Route::post('/preloved/sold', [PrelovedController::class, 'markSold'])->name('preloved.sold');
});

// ─── DONASI BUKU ───────────────────────────────────────────
Route::get('/donasi', [DonasiController::class, 'index'])->name('donasi.index');
    Route::get('/donasi/{campaign}', [DonasiController::class, 'show'])->name('donasi.show')->where('campaign', '[0-9]+');
    
    // Semua user login bisa donasikan buku & update resi
    Route::middleware('auth')->group(function () {
        Route::get('/donasi/{campaign}/donate', [DonasiController::class, 'createDonasi']);
        Route::post('/donasi/{campaign}/donate', [DonasiController::class, 'storeDonasi']);
        Route::get('/donasi/{campaign}/success', [DonasiController::class, 'donasiSuccess']);
        Route::post('/donasi/update-resi', [DonasiController::class, 'updateResi'])->name('donasi.updateResi');
        Route::post('/donasi/laporkan-mitra', [DonasiController::class, 'laporkanMitra'])->name('donasi.laporkanMitra');
    });

    // Hanya Mitra (role 4) — dashboard & lihat daftar donasi masuk
    Route::middleware(['auth', 'mitra'])->group(function () {
        Route::get('/donasi/dashboard', [DonasiController::class, 'mitraDashboard'])->name('donasi.dashboard');
        Route::get('/donasi/campaign/{id}/donations', [DonasiController::class, 'campaignDonations'])->name('donasi.campaign.donations');
    });

    // Hanya Mitra yang sudah VERIFIED — buat kampanye & konfirmasi terima buku
    Route::middleware(['auth', 'mitra', 'mitra.verified'])->group(function () {
        Route::get('/donasi/campaign/create', [DonasiController::class, 'createCampaign'])->name('donasi.campaign.create');
        Route::post('/donasi/campaign/create', [DonasiController::class, 'storeCampaign'])->name('donasi.campaign.store');
        Route::post('/donasi/konfirmasi', [DonasiController::class, 'konfirmasiDiterima'])->name('donasi.konfirmasi');
    });

// ─── PROFILE ───────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'update']);
});

// ─── NOTIFICATIONS ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/api/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');
});

// ─── ADMIN: SUPERADMIN ─────────────────────────────────────
Route::prefix('admin/superadmin')->middleware(['auth', 'role:1'])->group(function () {
    Route::get('/', [SuperadminController::class, 'dashboard'])->name('admin.superadmin.dashboard');

    Route::get('/books', [SuperadminController::class, 'books'])->name('admin.superadmin.books');
    Route::post('/books/create', [SuperadminController::class, 'createBook'])->name('admin.superadmin.books.create');
    Route::get('/books/{id}/edit', [SuperadminController::class, 'editBookForm'])->name('admin.superadmin.books.editform')->where('id', '[0-9]+');
    Route::post('/books/{id}/edit', [SuperadminController::class, 'editBook'])->name('admin.superadmin.books.edit');
    Route::post('/books/delete', [SuperadminController::class, 'deleteBook'])->name('admin.superadmin.books.delete');

    Route::get('/users', [SuperadminController::class, 'users'])->name('admin.superadmin.users');
    Route::get('/users/detail/{id}', [SuperadminController::class, 'detailUser'])->name('admin.superadmin.users.detail')->where('id', '[0-9]+');
    Route::post('/users/delete', [SuperadminController::class, 'deleteUser'])->name('admin.superadmin.users.delete');
    Route::post('/users/suspend', [SuperadminController::class, 'suspendUser'])->name('admin.superadmin.users.suspend');
    Route::get('/users/{id}/edit', [SuperadminController::class, 'editUserForm'])->name('admin.superadmin.users.editform')->where('id', '[0-9]+');
    Route::post('/users/{id}/edit', [SuperadminController::class, 'editUser'])->name('admin.superadmin.users.edit');

    Route::get('/communities', [SuperadminController::class, 'communities'])->name('admin.superadmin.communities');
    Route::get('/communities/detail/{id}', [SuperadminController::class, 'detailCommunity'])->name('admin.superadmin.communities.detail')->where('id', '[0-9]+');
    Route::post('/communities/{id}/approve', [SuperadminController::class, 'approveCommunity'])->name('admin.superadmin.communities.approve');
    Route::post('/communities/approve', [SuperadminController::class, 'approveCommunity']);
    Route::post('/communities/reject', [SuperadminController::class, 'rejectCommunity'])->name('admin.superadmin.communities.reject');
    Route::post('/communities/suspend', [SuperadminController::class, 'suspendCommunity'])->name('admin.superadmin.communities.suspend');

    Route::get('/preloved', [SuperadminController::class, 'preloved'])->name('admin.superadmin.preloved');
    Route::get('/preloved/detail/{id}', [SuperadminController::class, 'detailPreloved'])->name('admin.superadmin.preloved.detail')->where('id', '[0-9]+');
    Route::post('/preloved/suspend', [SuperadminController::class, 'suspendListing'])->name('admin.superadmin.preloved.suspend');
    Route::post('/preloved/delete', [SuperadminController::class, 'deleteListing'])->name('admin.superadmin.preloved.delete');

    // Genre CRUD
    Route::get('/genres', [SuperadminController::class, 'genres'])->name('admin.superadmin.genres');
    Route::post('/genres/create', [SuperadminController::class, 'createGenre'])->name('admin.superadmin.genres.create');
    Route::post('/genres/{id}/edit', [SuperadminController::class, 'editGenre'])->name('admin.superadmin.genres.edit');
    Route::post('/genres/delete', [SuperadminController::class, 'deleteGenre'])->name('admin.superadmin.genres.delete');

    // Tong Sampah
    Route::get('/trash', [SuperadminController::class, 'trash'])->name('admin.superadmin.trash');
    Route::post('/trash/book/restore', [SuperadminController::class, 'restoreBook'])->name('admin.superadmin.trash.book.restore');
    Route::post('/trash/book/force-delete', [SuperadminController::class, 'forceDeleteBook'])->name('admin.superadmin.trash.book.delete');
    Route::post('/trash/post/restore', [SuperadminController::class, 'restorePost'])->name('admin.superadmin.trash.post.restore');
    Route::post('/trash/post/force-delete', [SuperadminController::class, 'forceDeletePost'])->name('admin.superadmin.trash.post.delete');

    // Pusat Laporan Global
    Route::get('/reports', [SuperadminController::class, 'reports'])->name('admin.superadmin.reports');
    Route::post('/reports/resolve', [SuperadminController::class, 'resolveReport'])->name('admin.superadmin.reports.resolve');

    // Broadcast Notifikasi
    Route::get('/broadcast', [SuperadminController::class, 'broadcastForm'])->name('admin.superadmin.broadcast');
    Route::post('/broadcast', [SuperadminController::class, 'sendBroadcast'])->name('admin.superadmin.broadcast.send');

    // Settings
    // Profanities (Kamus Kata Kasar)
    Route::get('/profanity', [SuperadminController::class, 'profanities'])->name('admin.superadmin.profanity');
    Route::post('/profanity/add', [SuperadminController::class, 'addProfanity'])->name('admin.superadmin.profanity.add');
    Route::post('/profanity/delete', [SuperadminController::class, 'deleteProfanity'])->name('admin.superadmin.profanity.delete');
    
    // Instansi Daerah (Wilayah Mitra)
    Route::get('/instansi-daerah', [SuperadminController::class, 'instansiDaerah'])->name('admin.superadmin.instansidaerah');
    Route::post('/instansi-daerah/create', [SuperadminController::class, 'createInstansiDaerah'])->name('admin.superadmin.instansidaerah.create');
    Route::post('/instansi-daerah/{id}/edit', [SuperadminController::class, 'editInstansiDaerah'])->name('admin.superadmin.instansidaerah.edit');
    Route::post('/instansi-daerah/delete', [SuperadminController::class, 'deleteInstansiDaerah'])->name('admin.superadmin.instansidaerah.delete');

    // Logs & Settings
    Route::get('/logs', [SuperadminController::class, 'logs'])->name('admin.superadmin.logs');
    Route::get('/settings', [SuperadminController::class, 'settings'])->name('admin.superadmin.settings');
    Route::post('/settings', [SuperadminController::class, 'updateSettings'])->name('admin.superadmin.settings.update');
    
    // Verifikasi Mitra & Kampanye Donasi
    Route::get('/mitra', [SuperadminController::class, 'verifikasiMitra'])->name('admin.superadmin.mitra');
    Route::get('/mitra/{id}', [SuperadminController::class, 'detailMitra'])->name('admin.superadmin.mitra.detail')->where('id', '[0-9]+');
    Route::post('/mitra/approve', [SuperadminController::class, 'approveMitra'])->name('admin.superadmin.mitra.approve');
    Route::post('/mitra/suspend', [SuperadminController::class, 'suspendMitra'])->name('admin.superadmin.mitra.suspend');
    Route::post('/mitra/reject', [SuperadminController::class, 'rejectMitra'])->name('admin.superadmin.mitra.reject');
    Route::post('/mitra/delete', [SuperadminController::class, 'deleteMitra'])->name('admin.superadmin.mitra.delete');
    
    Route::get('/campaign/{id}', [SuperadminController::class, 'detailCampaign'])->name('admin.superadmin.campaign.detail')->where('id', '[0-9]+');
    Route::post('/campaign/approve', [SuperadminController::class, 'approveCampaignDonasi'])->name('admin.superadmin.campaign.approve');
    Route::post('/campaign/reject', [SuperadminController::class, 'rejectCampaignDonasi'])->name('admin.superadmin.campaign.reject');

    // Kelola Panduan
    Route::get('/panduan', [SuperadminController::class, 'panduans'])->name('admin.superadmin.panduan');
    Route::post('/panduan/create', [SuperadminController::class, 'createPanduan'])->name('admin.superadmin.panduan.create');
    Route::post('/panduan/{id}/edit', [SuperadminController::class, 'editPanduan'])->name('admin.superadmin.panduan.edit');
    Route::post('/panduan/delete', [SuperadminController::class, 'deletePanduan'])->name('admin.superadmin.panduan.delete');

    // Kelola Kategori Panduan
    Route::get('/kategori-panduan', [SuperadminController::class, 'kategoriPanduans'])->name('admin.superadmin.kategori-panduan');
    Route::post('/kategori-panduan/create', [SuperadminController::class, 'createKategoriPanduan'])->name('admin.superadmin.kategori-panduan.create');
    Route::post('/kategori-panduan/{id}/edit', [SuperadminController::class, 'editKategoriPanduan'])->name('admin.superadmin.kategori-panduan.edit');
    Route::post('/kategori-panduan/delete', [SuperadminController::class, 'deleteKategoriPanduan'])->name('admin.superadmin.kategori-panduan.delete');
});

// ─── ADMIN: ADMIN KOMUNITAS ────────────────────────────────
Route::prefix('admin/komunitas')->middleware(['auth', 'role:2'])->group(function () {
    Route::get('/', [AdminKomunitasController::class, 'dashboard'])->name('admin.komunitas.dashboard');
    Route::get('/{id}/members', [AdminKomunitasController::class, 'members'])->name('admin.komunitas.members');
    Route::post('/members/approve', [AdminKomunitasController::class, 'approveMember'])->name('admin.komunitas.members.approve');
    Route::post('/members/reject', [AdminKomunitasController::class, 'rejectMember'])->name('admin.komunitas.members.reject');
    Route::post('/members/kick', [AdminKomunitasController::class, 'kickMember'])->name('admin.komunitas.members.kick');
    Route::get('/{id}/moderation', [AdminKomunitasController::class, 'moderation'])->name('admin.komunitas.moderation');
    Route::post('/moderation/delete-post', [AdminKomunitasController::class, 'deletePost'])->name('admin.komunitas.moderation.deletePost');
    Route::post('/moderation/delete-comment', [AdminKomunitasController::class, 'deleteComment'])->name('admin.komunitas.moderation.deleteComment');

    // Premium Features Routes
    Route::post('/post/pin', [AdminKomunitasController::class, 'pinPost'])->name('admin.komunitas.post.pin');
    Route::post('/{id}/broadcast', [AdminKomunitasController::class, 'broadcast'])->name('admin.komunitas.broadcast');

    Route::get('/{id}/reports', [AdminKomunitasController::class, 'reports'])->name('admin.komunitas.reports');
    Route::post('/reports/resolve', [AdminKomunitasController::class, 'resolveReport'])->name('admin.komunitas.reports.resolve');

    Route::get('/{id}/events', [AdminKomunitasController::class, 'events'])->name('admin.komunitas.events');
    Route::post('/{id}/events/create', [AdminKomunitasController::class, 'createEvent'])->name('admin.komunitas.events.create');
    Route::post('/events/delete/{eventId}', [AdminKomunitasController::class, 'deleteEvent'])->name('admin.komunitas.events.delete');

    Route::get('/{id}/settings', [AdminKomunitasController::class, 'settings'])->name('admin.komunitas.settings');
    Route::post('/{id}/settings', [AdminKomunitasController::class, 'updateSettings'])->name('admin.komunitas.settings.update');

    // Phase 2: Gamifikasi, Rak Buku, Challenges, Q&A
    Route::get('/{id}/badges', [AdminKomunitasController::class, 'badges'])->name('admin.komunitas.badges');
    Route::post('/{id}/badges/store', [AdminKomunitasController::class, 'storeBadge'])->name('admin.komunitas.badges.store');
    Route::post('/{id}/badges/award', [AdminKomunitasController::class, 'awardBadge'])->name('admin.komunitas.badges.award');

    Route::get('/{id}/bookshelf', [AdminKomunitasController::class, 'bookshelf'])->name('admin.komunitas.bookshelf');
    Route::post('/{id}/bookshelf/add', [AdminKomunitasController::class, 'addBookshelf'])->name('admin.komunitas.bookshelf.add');
    Route::post('/{id}/bookshelf/remove', [AdminKomunitasController::class, 'removeBookshelf'])->name('admin.komunitas.bookshelf.remove');

    Route::get('/{id}/challenges', [AdminKomunitasController::class, 'challenges'])->name('admin.komunitas.challenges');
    Route::post('/{id}/challenges/create', [AdminKomunitasController::class, 'createChallenge'])->name('admin.komunitas.challenges.create');

    Route::get('/{id}/qna', [AdminKomunitasController::class, 'qna'])->name('admin.komunitas.qna');
    Route::post('/{id}/qna/create', [AdminKomunitasController::class, 'createQna'])->name('admin.komunitas.qna.create');
    Route::post('/{id}/qna/answer', [AdminKomunitasController::class, 'answerQna'])->name('admin.komunitas.qna.answer');
});
