<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKomunitas;
use App\Models\KomentarPostingan;
use App\Models\Komunitas;
use App\Models\Notifikasi;
use App\Models\PostinganKomunitas;
use App\Models\LaporanKomunitas;
use App\Models\EventKomunitas;
use App\Models\LencanaKomunitas;
use App\Models\UserLencana;
use App\Models\RakBukuKomunitas;
use App\Models\TantanganMembaca;
use App\Models\PesertaTantangan;
use App\Models\QnaKomunitas;
use App\Models\PertanyaanQna;
use App\Models\Buku;
use Illuminate\Http\Request;

class AdminKomunitasController extends Controller
{
    public function dashboard()
    {
        $communities = Komunitas::where('creator_id', auth()->id())
            ->where('status', 'aktif')
            ->get();

        $pendingCommunities = Komunitas::where('creator_id', auth()->id())
            ->where('status', 'pending')
            ->get();

        $stats = [];
        foreach ($communities as $c) {
            $stats[] = [
                'community'     => $c,
                'member_count'  => AnggotaKomunitas::where('komunitas_id', $c->id)->where('status', 'approved')->count(),
                'pending_count' => AnggotaKomunitas::where('komunitas_id', $c->id)->where('status', 'pending')->count(),
                'post_count'    => PostinganKomunitas::where('komunitas_id', $c->id)->count(),
            ];
        }

        return view('admin.community_admin.dashboard', compact('stats', 'pendingCommunities'));
    }

    public function members(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $pendingMembers = AnggotaKomunitas::where('komunitas_id', $komunitasId)
            ->where('status', 'pending')
            ->with('user:id,username,email,foto_profil')
            ->get();

        $approvedMembers = AnggotaKomunitas::where('komunitas_id', $komunitasId)
            ->where('status', 'approved')
            ->with('user:id,username,email,foto_profil')
            ->get();

        return view('admin.community_admin.members', compact('community', 'pendingMembers', 'approvedMembers'));
    }

    public function approveMember(Request $request)
    {
        $memberId = (int) $request->member_id;
        $member = AnggotaKomunitas::with('komunitas')->find($memberId);

        if (!$member || $member->komunitas->creator_id !== auth()->id()) abort(403);

        $member->update(['status' => 'approved']);

        Notifikasi::create([
            'user_id'    => $member->user_id,
            'tipe'       => 'bergabung_disetujui',
            'pesan'      => 'Anda telah diterima di komunitas "' . $member->komunitas->nama_komunitas . '"!',
            'url_target' => "/community/{$member->komunitas_id}/feed",
        ]);

        return redirect("/admin/komunitas/{$member->komunitas_id}/members")->with('success', 'Anggota disetujui!');
    }

    public function rejectMember(Request $request)
    {
        $memberId = (int) $request->member_id;
        $member = AnggotaKomunitas::with('komunitas')->find($memberId);

        if (!$member || $member->komunitas->creator_id !== auth()->id()) abort(403);

        $member->update(['status' => 'rejected']);

        Notifikasi::create([
            'user_id'    => $member->user_id,
            'tipe'       => 'bergabung_ditolak',
            'pesan'      => 'Permintaan bergabung Anda di "' . $member->komunitas->nama_komunitas . '" ditolak.',
            'url_target' => null,
        ]);

        return redirect("/admin/komunitas/{$member->komunitas_id}/members")->with('success', 'Permintaan ditolak.');
    }

    public function kickMember(Request $request)
    {
        $memberId = (int) $request->member_id;
        $member = AnggotaKomunitas::with('komunitas')->find($memberId);

        if (!$member || $member->komunitas->creator_id !== auth()->id()) abort(403);

        $member->delete();

        Notifikasi::create([
            'user_id'    => $member->user_id,
            'tipe'       => 'dikeluarkan_komunitas',
            'pesan'      => 'Anda telah dikeluarkan dari komunitas "' . $member->komunitas->nama_komunitas . '" oleh admin karena melanggar peraturan.',
            'url_target' => null,
        ]);

        return redirect("/admin/komunitas/{$member->komunitas_id}/members")->with('success', 'Anggota berhasil dikeluarkan.');
    }

    public function moderation(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        // Cek kepemilikan DAN status aktif — admin komunitas suspended tidak bisa moderasi
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') {
            abort(403, 'Akses ditolak. Komunitas tidak aktif atau bukan milik Anda.');
        }

        $posts = PostinganKomunitas::where('komunitas_id', $komunitasId)
            ->with('user:id,username,foto_profil')
            ->orderByDesc('created_at')
            ->get();

        $comments = KomentarPostingan::whereIn('postingan_id', $posts->pluck('id'))
            ->with('user:id,username,foto_profil')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.community_admin.moderation', compact('community', 'posts', 'comments'));
    }

    public function deletePost(Request $request)
    {
        $postId = (int) $request->post_id;
        $post = PostinganKomunitas::with('komunitas')->find($postId);

        if (!$post || $post->komunitas->creator_id !== auth()->id()) abort(403);

        Notifikasi::create([
            'user_id'    => $post->user_id,
            'tipe'       => 'postingan_dihapus',
            'pesan'      => 'Postingan Anda di "' . $post->komunitas->nama_komunitas . '" telah dihapus oleh admin.',
            'url_target' => null,
        ]);

        $post->delete();

        return redirect("/admin/komunitas/{$post->komunitas_id}/moderation")->with('success', 'Postingan dihapus.');
    }

    public function deleteComment(Request $request)
    {
        $commentId = (int) $request->comment_id;
        $comment = KomentarPostingan::with('postingan.komunitas')->find($commentId);

        if (!$comment || $comment->postingan->komunitas->creator_id !== auth()->id()) abort(403);

        $komunitasId = $comment->postingan->komunitas_id;
        $comment->delete();

        return redirect("/admin/komunitas/{$komunitasId}/moderation")->with('success', 'Komentar dihapus.');
    }

    // --- FITUR PENGUMUMAN & BROADCAST ---
    public function pinPost(Request $request)
    {
        $postId = (int) $request->post_id;
        $post = PostinganKomunitas::with('komunitas')->find($postId);

        if (!$post || $post->komunitas->creator_id !== auth()->id()) abort(403);

        $post->update(['is_pinned' => !$post->is_pinned]);
        $status = $post->is_pinned ? 'dipin' : 'dilepas pinnya';

        return back()->with('success', "Postingan berhasil $status.");
    }

    public function broadcast(Request $request, int $komunitasId)
    {
        $request->validate(['pesan' => 'required|string|max:255']);
        $community = Komunitas::find($komunitasId);
        
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') abort(403);

        $members = AnggotaKomunitas::where('komunitas_id', $komunitasId)->where('status', 'approved')->get();
        foreach ($members as $member) {
            Notifikasi::create([
                'user_id'    => $member->user_id,
                'tipe'       => 'pengumuman_komunitas',
                'pesan'      => "📢 Pengumuman dari {$community->nama_komunitas}: " . $request->pesan,
                'url_target' => "/community/{$komunitasId}/feed",
            ]);
        }

        return back()->with('success', 'Pengumuman berhasil di-broadcast ke semua anggota.');
    }

    // --- FITUR LAPORAN ---
    public function reports(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $reports = LaporanKomunitas::where('komunitas_id', $komunitasId)
            ->with(['pelapor:id,username', 'postingan:id,konten', 'komentar:id,konten'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.community_admin.reports', compact('community', 'reports'));
    }

    public function resolveReport(Request $request)
    {
        $reportId = (int) $request->report_id;
        $report = LaporanKomunitas::with('komunitas')->find($reportId);

        if (!$report || $report->komunitas->creator_id !== auth()->id()) abort(403);

        $action = $request->action; // 'delete_post', 'delete_comment', 'dismiss'

        if ($action === 'delete_post' && $report->postingan_id) {
            PostinganKomunitas::where('id', $report->postingan_id)->delete();
            $report->update(['status' => 'resolved']);
        } elseif ($action === 'delete_comment' && $report->komentar_id) {
            KomentarPostingan::where('id', $report->komentar_id)->delete();
            $report->update(['status' => 'resolved']);
        } elseif ($action === 'dismiss') {
            $report->update(['status' => 'dismissed']);
        }

        return back()->with('success', 'Laporan berhasil diproses.');
    }

    // --- FITUR EVENT ---
    public function events(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') abort(403);

        $events = EventKomunitas::where('komunitas_id', $komunitasId)->orderBy('tanggal_waktu', 'asc')->get();
        return view('admin.community_admin.events', compact('community', 'events'));
    }

    public function createEvent(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') abort(403);

        $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tanggal_waktu' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
        ]);

        EventKomunitas::create([
            'komunitas_id' => $komunitasId,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_waktu' => $request->tanggal_waktu,
            'lokasi' => $request->lokasi,
        ]);

        return back()->with('success', 'Jadwal event berhasil dibuat!');
    }

    public function deleteEvent(Request $request, int $eventId)
    {
        $event = EventKomunitas::with('komunitas')->find($eventId);
        if (!$event || $event->komunitas->creator_id !== auth()->id()) abort(403);

        $event->delete();
        return back()->with('success', 'Event dibatalkan.');
    }

    // --- FITUR KUSTOMISASI ---
    public function settings(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') abort(403);

        return view('admin.community_admin.settings', compact('community'));
    }

    public function updateSettings(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') abort(403);

        $request->validate([
            'tema_warna' => 'required|string|max:20',
        ]);

        $community->tema_warna = $request->tema_warna;
        $community->save();

        return back()->with('success', 'Tema warna komunitas berhasil diubah.');
    }

    // --- FITUR LENCANA (GAMIFIKASI) ---
    public function badges(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') abort(403);

        $badges = LencanaKomunitas::where('komunitas_id', $komunitasId)->get();
        $members = AnggotaKomunitas::where('komunitas_id', $komunitasId)->where('status', 'approved')->with('user')->get();
        
        return view('admin.community_admin.badges', compact('community', 'badges', 'members'));
    }

    public function storeBadge(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $request->validate([
            'nama_lencana' => 'required|string|max:100',
            'ikon' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
        ]);

        LencanaKomunitas::create([
            'komunitas_id' => $komunitasId,
            'nama_lencana' => $request->nama_lencana,
            'ikon' => $request->ikon ?? '🏆',
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Lencana berhasil dibuat.');
    }

    public function awardBadge(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'lencana_id' => 'required|exists:lencana_komunitas,id',
        ]);

        // Cek jika user sudah punya lencana ini
        $exists = UserLencana::where('user_id', $request->user_id)
            ->where('lencana_id', $request->lencana_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anggota sudah memiliki lencana ini.');
        }

        UserLencana::create([
            'user_id' => $request->user_id,
            'lencana_id' => $request->lencana_id,
        ]);

        Notifikasi::create([
            'user_id' => $request->user_id,
            'tipe' => 'lencana_baru',
            'pesan' => "Anda mendapatkan lencana baru di komunitas {$community->nama_komunitas}!",
            'url_target' => "/community/{$komunitasId}/feed",
        ]);

        return back()->with('success', 'Lencana berhasil diberikan.');
    }

    // --- FITUR RAK BUKU KOMUNITAS ---
    public function bookshelf(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $bookshelf = RakBukuKomunitas::where('komunitas_id', $komunitasId)->with('buku')->get();
        $books = Buku::all();

        return view('admin.community_admin.bookshelf', compact('community', 'bookshelf', 'books'));
    }

    public function addBookshelf(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'tipe' => 'required|in:wajib_baca,rekomendasi',
            'catatan_admin' => 'nullable|string',
        ]);

        RakBukuKomunitas::updateOrCreate(
            ['komunitas_id' => $komunitasId, 'buku_id' => $request->buku_id],
            ['tipe' => $request->tipe, 'catatan_admin' => $request->catatan_admin]
        );

        return back()->with('success', 'Buku berhasil ditambahkan ke rak komunitas.');
    }

    public function removeBookshelf(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        RakBukuKomunitas::where('komunitas_id', $komunitasId)
            ->where('buku_id', $request->buku_id)
            ->delete();

        return back()->with('success', 'Buku dihapus dari rak komunitas.');
    }

    // --- FITUR TANTANGAN MEMBACA ---
    public function challenges(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') abort(403);

        $challenges = TantanganMembaca::where('komunitas_id', $komunitasId)->withCount('peserta')->get();

        return view('admin.community_admin.challenges', compact('community', 'challenges'));
    }

    public function createChallenge(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'target_buku' => 'required|integer|min:1',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        TantanganMembaca::create([
            'komunitas_id' => $komunitasId,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'target_buku' => $request->target_buku,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return back()->with('success', 'Tantangan membaca berhasil dibuat.');
    }

    // --- FITUR Q&A ---
    public function qna(int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id() || $community->status !== 'aktif') abort(403);

        $qnas = QnaKomunitas::where('komunitas_id', $komunitasId)->with('pertanyaan.user')->get();

        return view('admin.community_admin.qna', compact('community', 'qnas'));
    }

    public function createQna(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $request->validate([
            'judul' => 'required|string|max:150',
            'narasumber' => 'nullable|string|max:100',
        ]);

        QnaKomunitas::create([
            'komunitas_id' => $komunitasId,
            'judul' => $request->judul,
            'narasumber' => $request->narasumber,
            'status' => 'open',
        ]);

        return back()->with('success', 'Sesi Q&A berhasil dibuka.');
    }

    public function answerQna(Request $request, int $komunitasId)
    {
        $community = Komunitas::find($komunitasId);
        if (!$community || $community->creator_id !== auth()->id()) abort(403);

        $request->validate([
            'pertanyaan_id' => 'required|exists:pertanyaan_qnas,id',
            'jawaban' => 'required|string',
        ]);

        $pertanyaan = PertanyaanQna::find($request->pertanyaan_id);
        $pertanyaan->update(['jawaban' => $request->jawaban]);

        Notifikasi::create([
            'user_id' => $pertanyaan->user_id,
            'tipe' => 'qna_dijawab',
            'pesan' => "Pertanyaan Anda di sesi Q&A komunitas {$community->nama_komunitas} telah dijawab!",
            'url_target' => "/community/{$komunitasId}/qna",
        ]);

        return back()->with('success', 'Jawaban berhasil dikirim.');
    }
}
