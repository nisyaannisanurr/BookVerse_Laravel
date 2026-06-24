<?php

namespace App\Http\Controllers;

use App\Models\AnggotaKomunitas;
use App\Models\Komunitas;
use App\Models\KomentarPostingan;
use App\Models\Notifikasi;
use App\Models\PostinganKomunitas;
use App\Models\LaporanKomunitas;
use App\Models\EventKomunitas;
use App\Models\RakBukuKomunitas;
use App\Models\TantanganMembaca;
use App\Models\QnaKomunitas;
use App\Models\PesertaTantangan;
use App\Models\PertanyaanQna;
use App\Services\FileUploadService;
use App\Services\ProfanityFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    protected FileUploadService $uploadService;
    protected ProfanityFilterService $profanityFilter;

    public function __construct(FileUploadService $uploadService, ProfanityFilterService $profanityFilter)
    {
        $this->uploadService = $uploadService;
        $this->profanityFilter = $profanityFilter;
    }

    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'explore');
        $search = trim($request->get('search', ''));
        $myCommunities = collect();

        if (auth()->check()) {
            $myCommunities = Komunitas::withMemberCount()
                ->whereHas('anggota', function ($q) {
                    $q->where('user_id', auth()->id())->where('status', 'approved');
                })
                ->where('komunitas.status', 'aktif')
                ->get();

            $created = Komunitas::withMemberCount()
                ->where('creator_id', auth()->id())
                ->where('status', 'aktif')
                ->get();

            $existingIds = $myCommunities->pluck('id')->toArray();
            foreach ($created as $cc) {
                if (!in_array($cc->id, $existingIds)) {
                    $myCommunities->push($cc);
                }
            }
        }

        $query = Komunitas::withMemberCount()->aktif();
        if ($search !== '') {
            $query->where('nama_komunitas', 'LIKE', '%'.$search.'%');
        }
        $allCommunities = $query->orderByDesc('created_at')->get();

        return view('community.index', compact('tab', 'myCommunities', 'allCommunities', 'search'));
    }

    public function gate(int $id)
    {
        $community = Komunitas::withMemberCount()->find($id);
        if (!$community || $community->status !== 'aktif') abort(404);

        $memberStatus = null;
        if (auth()->check()) {
            if (auth()->user()->isSuperadmin()) {
                return redirect("/community/{$id}/feed");
            }

            $memberStatus = AnggotaKomunitas::where('user_id', auth()->id())
                ->where('komunitas_id', $id)->value('status');

            if ($memberStatus === 'approved' || $community->creator_id == auth()->id()) {
                return redirect("/community/{$id}/feed");
            }
        }

        $members = AnggotaKomunitas::where('komunitas_id', $id)->where('status', 'approved')->with('user:id,username,foto_profil')->get();

        return view('community.gate', compact('community', 'memberStatus', 'members'));
    }

    public function join(Request $request, int $id)
    {
        $community = Komunitas::find($id);
        if (!$community || $community->status !== 'aktif') {
            return redirect('/community')->with('error', 'Komunitas tidak ditemukan.');
        }

        AnggotaKomunitas::updateOrCreate(
            ['user_id' => auth()->id(), 'komunitas_id' => $id],
            ['status' => 'pending']
        );

        \App\Models\LogAktivitas::record(auth()->id(), 'Gabung Komunitas', "Meminta bergabung ke komunitas '{$community->nama_komunitas}'");

        return redirect("/community/{$id}")->with('success', 'Permintaan bergabung telah dikirim. Tunggu persetujuan admin.');
    }

    public function leave(Request $request, int $id)
    {
        if (!auth()->check()) abort(403);

        $membership = AnggotaKomunitas::where('user_id', auth()->id())
            ->where('komunitas_id', $id)
            ->first();

        if (!$membership) {
            return back()->with('error', 'Anda bukan anggota komunitas ini.');
        }

        $membership->delete();

        $community = Komunitas::find($id);
        if ($community) {
            \App\Models\LogAktivitas::record(auth()->id(), 'Keluar Komunitas', "Keluar dari komunitas '{$community->nama_komunitas}'");
        }

        return redirect('/community')->with('success', 'Anda telah keluar dari komunitas.');
    }

    public function feed(int $id)
    {
        $community = Komunitas::withMemberCount()->find($id);
        if (!$community) abort(404);

        $posts = PostinganKomunitas::where('komunitas_id', $id)
            ->selectRaw('postingan_komunitas.*, (SELECT COUNT(*) FROM komentar_postingan WHERE postingan_id = postingan_komunitas.id) as comment_count')
            ->with(['user', 'user.lencana.lencana', 'likes'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $members = AnggotaKomunitas::where('komunitas_id', $id)->where('status', 'approved')->with(['user', 'user.lencana.lencana'])->get();

        $events = EventKomunitas::where('komunitas_id', $id)
            ->where('tanggal_waktu', '>=', now())
            ->orderBy('tanggal_waktu', 'asc')
            ->limit(5)
            ->get();

        $bookshelf = RakBukuKomunitas::where('komunitas_id', $id)->with('buku')->limit(5)->get();
        $challenges = TantanganMembaca::where('komunitas_id', $id)
            ->where(function($q) {
                $q->whereNull('tanggal_selesai')->orWhere('tanggal_selesai', '>=', now());
            })
            ->withCount('peserta')
            ->limit(3)->get();
        
        $qnas = QnaKomunitas::where('komunitas_id', $id)
            ->where('status', 'open')
            ->with('pertanyaan')
            ->limit(3)->get();

        $joinedChallengeIds = [];
        if (auth()->check()) {
            $joinedChallengeIds = PesertaTantangan::where('user_id', auth()->id())
                ->whereIn('tantangan_id', $challenges->pluck('id'))
                ->pluck('tantangan_id')
                ->toArray();
        }

        $topMembers = \App\Models\User::select('users.id', 'users.username', 'users.foto_profil')
            ->selectRaw('COUNT(postingan_komunitas.id) as post_count')
            ->join('postingan_komunitas', 'users.id', '=', 'postingan_komunitas.user_id')
            ->where('postingan_komunitas.komunitas_id', $id)
            ->groupBy('users.id', 'users.username', 'users.foto_profil')
            ->orderByDesc('post_count')
            ->limit(5)
            ->get();

        return view('community.feed', compact('community', 'posts', 'members', 'events', 'bookshelf', 'challenges', 'qnas', 'joinedChallengeIds', 'topMembers'));
    }

    public function joinChallenge(Request $request, int $komunitasId)
    {
        if (!auth()->check()) abort(403);

        $request->validate([
            'tantangan_id' => 'required|exists:tantangan_membacas,id',
        ]);

        PesertaTantangan::firstOrCreate([
            'tantangan_id' => $request->tantangan_id,
            'user_id' => auth()->id()
        ], [
            'buku_dibaca' => 0,
            'status' => 'ongoing'
        ]);

        \App\Models\LogAktivitas::record(auth()->id(), 'Tantangan Komunitas', "Bergabung dengan tantangan membaca di komunitas");

        return back()->with('success', 'Berhasil bergabung dengan tantangan membaca!');
    }

    public function showChallenge(int $komunitasId, int $challengeId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community) abort(404);

        $challenge = TantanganMembaca::with(['peserta.user', 'postingan' => function($q) {
            $q->orderBy('created_at', 'desc');
        }, 'postingan.user', 'postingan.likes'])->find($challengeId);

        if (!$challenge || $challenge->komunitas_id !== $komunitasId) abort(404);

        $pesertaQuery = PesertaTantangan::where('tantangan_id', $challengeId)
            ->with(['user', 'user.lencana.lencana'])
            ->orderBy('buku_dibaca', 'desc')
            ->get();

        return view('community.challenge_show', compact('community', 'challenge', 'pesertaQuery'));
    }

    public function askQna(Request $request, int $komunitasId)
    {
        if (!auth()->check()) abort(403);

        $request->validate([
            'qna_id' => 'required|exists:qna_komunitas,id',
            'pertanyaan' => 'required|string|max:500',
        ]);

        PertanyaanQna::create([
            'qna_id' => $request->qna_id,
            'user_id' => auth()->id(),
            'pertanyaan' => $this->profanityFilter->filter($request->pertanyaan),
        ]);

        \App\Models\LogAktivitas::record(auth()->id(), 'Tanya QnA Komunitas', "Mengajukan pertanyaan pada sesi QnA komunitas");

        return back()->with('success', 'Pertanyaan berhasil diajukan!');
    }

    public function createPost(Request $request, int $komunitasId)
    {
        $konten = trim($request->konten ?? '');
        if (empty($konten)) {
            return redirect("/community/{$komunitasId}/feed")->with('error', 'Konten tidak boleh kosong.');
        }

        $gambarPaths = [];
        if ($request->hasFile('gambar')) {
            $files = is_array($request->file('gambar')) ? $request->file('gambar') : [$request->file('gambar')];
            foreach ($files as $file) {
                $path = $this->uploadService->uploadImage($file, 'post_images');
                if ($path) {
                    $gambarPaths[] = $path;
                }
            }
        }

        $judul = trim($request->judul ?? '') ?: null;
        if ($judul) {
            $judul = $this->profanityFilter->filter($judul);
        }

        $tantanganId = $request->tantangan_id ?: null;

        $postingan = PostinganKomunitas::create([
            'komunitas_id' => $komunitasId,
            'user_id'      => auth()->id(),
            'judul'        => $judul,
            'konten'       => $this->profanityFilter->filter($konten),
            'gambar'       => empty($gambarPaths) ? null : json_encode($gambarPaths),
            'tantangan_id' => $tantanganId,
        ]);

        if ($tantanganId) {
            $peserta = \App\Models\PesertaTantangan::where('tantangan_id', $tantanganId)
                        ->where('user_id', auth()->id())
                        ->where('status', 'ongoing')
                        ->first();
            
            if ($peserta) {
                $peserta->increment('buku_dibaca');
                
                $tantangan = \App\Models\TantanganMembaca::find($tantanganId);
                if ($tantangan && $peserta->buku_dibaca >= $tantangan->target_buku) {
                    $peserta->status = 'completed';
                    $peserta->save();
                    \App\Models\LogAktivitas::record(auth()->id(), 'Tantangan Selesai', "Menyelesaikan tantangan membaca: {$tantangan->judul}");
                }
            }
        }

        $komunitas = Komunitas::find($komunitasId);
        if ($komunitas) {
            \App\Models\LogAktivitas::record(auth()->id(), 'Create Postingan Komunitas', "Membuat postingan baru di komunitas '{$komunitas->nama_komunitas}'");
        }

        return redirect("/community/{$komunitasId}/feed")->with('success', 'Postingan berhasil dibuat!');
    }

    public function postDetail(int $postId)
    {
        $post = PostinganKomunitas::with(['user', 'user.lencana.lencana', 'komunitas:id,nama_komunitas,creator_id', 'likes'])->find($postId);
        if (!$post) abort(404);

        $comments = KomentarPostingan::where('postingan_id', $postId)
            ->with(['user', 'user.lencana.lencana'])
            ->orderBy('created_at')
            ->get();

        $community = $post->komunitas;

        return view('community.post_detail', compact('post', 'comments', 'community'));
    }

    public function addComment(Request $request)
    {
        $postId = (int) $request->postingan_id;
        $konten = trim($request->konten ?? '');

        $post = PostinganKomunitas::find($postId);
        if (!$post) {
            return redirect('/community')->with('error', 'Postingan tidak ditemukan.');
        }

        if (empty($konten) && !$request->hasFile('gambar')) {
            return redirect("/community/post/{$postId}")->with('error', 'Komentar tidak boleh kosong.');
        }

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $this->uploadService->uploadImage($request->file('gambar'), 'comment_images');
            if (!$gambarPath) {
                return redirect("/community/post/{$postId}")->with('error', 'Gagal mengunggah gambar.');
            }
        }

        KomentarPostingan::create([
            'postingan_id' => $postId,
            'user_id'      => auth()->id(),
            'konten'       => $this->profanityFilter->filter($konten),
            'gambar'       => $gambarPath,
        ]);

        \App\Models\LogAktivitas::record(auth()->id(), 'Komentar Komunitas', "Mengomentari postingan di komunitas");

        return redirect("/community/post/{$postId}")->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function likePost(Request $request, int $id)
    {
        if (!auth()->check()) abort(403);

        $post = PostinganKomunitas::find($id);
        if (!$post) {
            return back()->with('error', 'Postingan tidak ditemukan.');
        }

        $userId = auth()->id();
        $like = \App\Models\PostinganLike::where('postingan_id', $id)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
        } else {
            \App\Models\PostinganLike::create([
                'postingan_id' => $id,
                'user_id' => $userId
            ]);
            \App\Models\LogAktivitas::record(auth()->id(), 'Like Postingan', "Menyukai postingan di komunitas");
        }

        return back();
    }

    public function deletePost(Request $request)
    {
        $postId = (int) $request->post_id;
        $post = PostinganKomunitas::with('komunitas')->find($postId);

        if (!$post) {
            return redirect('/community')->with('error', 'Postingan tidak ditemukan.');
        }

        $community = $post->komunitas;
        $canDelete = auth()->id() === $post->user_id || auth()->user()->isSuperadmin() || ($community && $community->creator_id == auth()->id());

        if (!$canDelete) abort(403);

        if (auth()->id() !== $post->user_id) {
            Notifikasi::create([
                'user_id'    => $post->user_id,
                'tipe'       => 'postingan_dihapus',
                'pesan'      => 'Postingan Anda "' . Str::limit($post->konten, 50) . '" telah dihapus oleh admin.',
                'url_target' => null,
            ]);
        }

        $post->delete();

        return redirect("/community/{$post->komunitas_id}/feed")->with('success', 'Postingan berhasil dihapus.');
    }

    public function deleteComment(Request $request)
    {
        $commentId = (int) $request->comment_id;
        $comment = KomentarPostingan::with(['postingan.komunitas'])->find($commentId);

        if (!$comment) {
            return redirect('/community')->with('error', 'Komentar tidak ditemukan.');
        }

        $community = $comment->postingan->komunitas;
        $canDelete = auth()->id() === $comment->user_id || auth()->user()->isSuperadmin() || ($community && $community->creator_id == auth()->id());

        if (!$canDelete) abort(403);

        $postId = $comment->postingan_id;
        $comment->delete();

        return redirect("/community/post/{$postId}")->with('success', 'Komentar berhasil dihapus.');
    }

    public function showCreateForm()
    {
        // Guard: harus login dulu
        if (!auth()->check()) {
            return redirect('/login')
                ->with('info', 'Kamu harus login atau daftar dulu untuk mengajukan komunitas.');
        }

        return view('community.create');
    }

    public function create(Request $request)
    {
        $nama = trim($request->nama_komunitas ?? '');
        $deskripsi = trim($request->deskripsi ?? '');
        $peraturan = trim($request->peraturan ?? '');

        if (empty($nama) || empty($deskripsi)) {
            return redirect('/community/create')->with('error', 'Nama dan deskripsi komunitas harus diisi.');
        }

        if (!$request->hasFile('banner')) {
            return redirect('/community/create')->with('error', 'Banner komunitas harus diunggah.');
        }

        $banner = $this->uploadService->uploadImage($request->file('banner'), 'banners');
        if (!$banner) {
            return redirect('/community/create')->with('error', 'Gagal mengunggah banner. Pastikan file adalah gambar (JPG/PNG/WEBP) dan ukuran maksimal 5MB.');
        }

        // Guard: harus login dulu
        if (!auth()->check()) {
            return redirect('/login')
                ->with('info', 'Login dulu untuk mengajukan komunitas.');
        }

        $community = Komunitas::create([
            'nama_komunitas'   => $nama,
            'deskripsi'        => $deskripsi,
            'peraturan'        => $peraturan ?: null,
            'banner_komunitas' => $banner,
            'creator_id'       => auth()->id(),
            'status'           => 'pending',
        ]);

        // TIDAK upgrade role di sini — role akan diupgrade oleh Superadmin
        // saat komunitas di-approve via SuperadminController::approveCommunity()

        \App\Models\LogAktivitas::record(auth()->id(), 'Create Komunitas', "Mengajukan pembuatan komunitas: {$community->nama_komunitas}");

        return redirect('/community')
            ->with('success', 'Komunitas berhasil diajukan! Menunggu persetujuan Superadmin.');
    }

    public function report(Request $request)
    {
        $request->validate([
            'komunitas_id' => 'required|integer',
            'alasan'       => 'required|string|max:1000',
        ]);

        $komunitasId = $request->komunitas_id;
        $postinganId = $request->postingan_id;
        $komentarId  = $request->komentar_id;

        if (!$postinganId && !$komentarId) {
            return back()->with('error', 'Pilih postingan atau komentar yang ingin dilaporkan.');
        }

        LaporanKomunitas::create([
            'komunitas_id' => $komunitasId,
            'pelapor_id'   => auth()->id(),
            'postingan_id' => $postinganId ?: null,
            'komentar_id'  => $komentarId ?: null,
            'alasan'       => $request->alasan,
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Laporan berhasil dikirim ke Admin Komunitas.');
    }

    public function chatIndex(int $id)
    {
        $community = Komunitas::findOrFail($id);
        
        // Ensure user is member
        if (!auth()->user()->isSuperadmin() && $community->creator_id !== auth()->id()) {
            $isMember = AnggotaKomunitas::where('komunitas_id', $id)->where('user_id', auth()->id())->exists();
            if (!$isMember) {
                return redirect("/community/{$id}")->with('error', 'Anda harus menjadi anggota untuk mengakses chat.');
            }
        }

        return view('community.chat', compact('community'));
    }

    public function fetchChats(int $id, Request $request)
    {
        $lastId = $request->query('last_id', 0);
        
        $chats = \App\Models\KomunitasChat::with('user', 'parent.user')
            ->where('komunitas_id', $id)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($chat) {
                $mediaList = [];
                if (!empty($chat->media_path)) {
                    $paths = is_array($chat->media_path) ? $chat->media_path : [$chat->media_path];
                    $types = is_array($chat->media_type) ? $chat->media_type : [$chat->media_type];
                    foreach ($paths as $index => $path) {
                        if ($path) {
                            $mediaList[] = [
                                'url' => asset('storage/' . $path),
                                'type' => $types[$index] ?? 'image'
                            ];
                        }
                    }
                }

                return [
                    'id' => $chat->id,
                    'user_id' => $chat->user_id,
                    'username' => $chat->user->username,
                    'foto_profil' => $chat->user->foto_profil ? \App\Helpers\BookVerseHelper::uploadUrl('profiles', $chat->user->foto_profil) : null,
                    'pesan' => $chat->pesan,
                    'media' => $mediaList,
                    'waktu' => $chat->created_at->format('H:i'),
                    'is_mine' => (int)$chat->user_id === (int)auth()->id(),
                    'reactions' => $chat->reactions ?? [],
                    'parent' => $chat->parent ? [
                        'username' => $chat->parent->user->username,
                        'pesan' => \Illuminate\Support\Str::limit($chat->parent->pesan, 50)
                    ] : null
                ];
            });

        return response()->json([
            'chats' => $chats,
            'last_id' => $chats->isNotEmpty() ? $chats->last()['id'] : $lastId
        ]);
    }

    public function sendChat(int $id, Request $request)
    {
        $request->validate([
            'pesan' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|integer|exists:komunitas_chats,id',
            'media' => 'nullable|array|max:10',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240', // max 10MB
        ]);

        if (empty($request->pesan) && !$request->hasFile('media')) {
            return response()->json(['error' => 'Pesan atau media harus diisi'], 422);
        }

        $mediaPaths = [];
        $mediaTypes = [];

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $mediaTypes[] = in_array($extension, ['mp4', 'mov', 'avi']) ? 'video' : 'image';
                    $mediaPaths[] = $file->store('chat_media', 'public');
                }
            }
        }

        $chat = \App\Models\KomunitasChat::create([
            'komunitas_id' => $id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'pesan' => $this->profanityFilter->filter($request->pesan ?? ''),
            'media_path' => empty($mediaPaths) ? null : $mediaPaths,
            'media_type' => empty($mediaTypes) ? null : $mediaTypes,
        ]);

        return response()->json(['success' => true]);
    }

    public function reactChat(int $id, int $chatId, Request $request)
    {
        $request->validate(['emoji' => 'required|string']);
        
        $chat = \App\Models\KomunitasChat::where('komunitas_id', $id)->findOrFail($chatId);
        $emoji = $request->emoji;
        $userId = auth()->id();
        
        $reactions = $chat->reactions ?? [];
        
        if (!isset($reactions[$emoji])) {
            $reactions[$emoji] = [];
        }
        
        // Toggle user reaction
        if (in_array($userId, $reactions[$emoji])) {
            $reactions[$emoji] = array_values(array_diff($reactions[$emoji], [$userId]));
            if (empty($reactions[$emoji])) {
                unset($reactions[$emoji]);
            }
        } else {
            $reactions[$emoji][] = $userId;
        }
        
        if (empty($reactions)) {
            $chat->reactions = null;
        } else {
            $chat->reactions = $reactions;
        }
        
        $chat->save();
        
        return response()->json(['success' => true, 'reactions' => $reactions]);
    }
}
