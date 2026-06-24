<?php

namespace App\Http\Controllers;

use App\Models\CampaignDonasi;
use App\Models\DonasiBuku;
use App\Models\MitraVerification;
use App\Models\Notifikasi;
use App\Models\LogAktivitas;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class DonasiController extends Controller
{
    protected FileUploadService $uploadService;

    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    // ─── PUBLIC: Katalog Kampanye ──────────────────────
    public function index(Request $request)
    {
        $query = CampaignDonasi::active()
            ->belumBerakhir()
            ->with('user.mitraVerification');

        if ($search = $request->get('search')) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('judul', 'LIKE', $like)
                  ->orWhere('deskripsi', 'LIKE', $like);
            });
        }

        if ($kategori = $request->get('kategori')) {
            $query->whereHas('user.mitraVerification', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });
        }

        $campaigns = $query->orderByDesc('created_at')->get();

        // Stats
        $totalCampaign    = CampaignDonasi::active()->count();
        $totalBukuDiterima = DonasiBuku::where('status_pengiriman', 'diterima')->sum('jumlah');
        $totalDonatur      = DonasiBuku::distinct('user_id')->count('user_id');

        return view('donasi.index', compact('campaigns', 'totalCampaign', 'totalBukuDiterima', 'totalDonatur'));
    }

    public function show(int $id)
    {
        $campaign = CampaignDonasi::with(['user.mitraVerification', 'donasiBuku.user'])->find($id);
        if (!$campaign) abort(404);

        $recentDonations = $campaign->donasiBuku()
            ->with('user:id,username,foto_profil')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('donasi.show', compact('campaign', 'recentDonations'));
    }

    // ─── USER: Form Donasi ────────────────────────────
    public function createDonasi(int $campaignId)
    {
        $campaign = CampaignDonasi::active()->find($campaignId);
        if (!$campaign) abort(404);

        return view('donasi.form-donasi', compact('campaign'));
    }

    public function storeDonasi(Request $request, int $campaignId)
    {
        $campaign = CampaignDonasi::active()->find($campaignId);
        if (!$campaign) abort(404);

        $judulBuku = trim($request->judul_buku ?? '');
        $jumlah    = max(1, (int) ($request->jumlah ?? 1));
        $kondisi   = $request->kondisi ?? 'bekas_layak';
        $catatan   = trim($request->catatan ?? '');
        $noWa      = trim($request->no_wa_donatur ?? '');

        if (empty($judulBuku) || empty($noWa)) {
            return back()->with('error', 'Judul buku dan Nomor WhatsApp harus diisi.')->withInput();
        }

        if (!in_array($kondisi, ['baru', 'bekas_layak'])) {
            $kondisi = 'bekas_layak';
        }

        $donasi = DonasiBuku::create([
            'campaign_id'      => $campaignId,
            'user_id'          => auth()->id(),
            'no_wa_donatur'    => $noWa,
            'judul_buku'       => $judulBuku,
            'jumlah'           => $jumlah,
            'kondisi'          => $kondisi,
            'catatan'          => $catatan,
            'status_pengiriman' => 'menunggu_dikirim',
        ]);

        // Notify mitra
        Notifikasi::create([
            'user_id'    => $campaign->user_id,
            'tipe'       => 'donasi_baru',
            'pesan'      => auth()->user()->username . ' akan mendonasikan ' . $jumlah . ' buku untuk kampanye "' . $campaign->judul . '"',
            'url_target' => '/donasi/dashboard',
        ]);

        LogAktivitas::record(auth()->id(), 'Donasi Buku', "Mendonasikan buku '{$judulBuku}' ({$jumlah} eksemplar) ke kampanye '{$campaign->judul}'");

        return redirect("/donasi/{$campaignId}/success?donasi_id={$donasi->id}");
    }

    public function donasiSuccess(Request $request, int $campaignId)
    {
        $campaign = CampaignDonasi::with('user.mitraVerification')->find($campaignId);
        $donasi = DonasiBuku::find($request->donasi_id);
        
        if (!$campaign || !$donasi || $donasi->user_id !== auth()->id()) {
            return redirect("/donasi/{$campaignId}");
        }

        return view('donasi.success', compact('campaign', 'donasi'));
    }

    public function updateResi(Request $request)
    {
        $donasiId = (int) $request->donasi_id;
        $donasi = DonasiBuku::find($donasiId);

        if (!$donasi || $donasi->user_id !== auth()->id()) abort(403);

        $resi = trim($request->resi_pengiriman ?? '');
        if (empty($resi)) {
            return back()->with('error', 'Nomor resi harus diisi.');
        }

        $donasi->update([
            'resi_pengiriman'   => $resi,
            'status_pengiriman' => 'dikirim',
        ]);

        // Notify mitra
        $campaign = $donasi->campaign;
        Notifikasi::create([
            'user_id'    => $campaign->user_id,
            'tipe'       => 'resi_diupdate',
            'pesan'      => auth()->user()->username . ' telah mengirim buku (Resi: ' . $resi . ') untuk kampanye "' . $campaign->judul . '"',
            'url_target' => '/donasi/dashboard',
        ]);

        return back()->with('success', 'Nomor resi berhasil diperbarui!');
    }

    // ─── MITRA: Dashboard ─────────────────────────────
    public function mitraDashboard()
    {
        $user = auth()->user();
        $verification = $user->mitraVerification;

        $campaigns = CampaignDonasi::where('user_id', $user->id)
            ->withCount('donasiBuku')
            ->orderByDesc('created_at')
            ->get();

        $totalBukuDiterima = 0;
        $totalDonatur      = 0;

        foreach ($campaigns as $c) {
            $totalBukuDiterima += $c->donasiBuku()->where('status_pengiriman', 'diterima')->sum('jumlah');
            $totalDonatur      += $c->donasiBuku()->distinct('user_id')->count('user_id');
        }

        return view('donasi.dashboard', compact('verification', 'campaigns', 'totalBukuDiterima', 'totalDonatur'));
    }

    // ─── MITRA: Buat Kampanye ─────────────────────────
    public function createCampaign()
    {
        if (!auth()->user()->isMitraVerified()) {
            return redirect('/donasi/dashboard')->with('error', 'Akun Anda belum diverifikasi. Silakan tunggu persetujuan admin.');
        }

        return view('donasi.form-campaign');
    }

    public function storeCampaign(Request $request)
    {
        if (!auth()->user()->isMitraVerified()) {
            return redirect('/donasi/dashboard')->with('error', 'Akun Anda belum diverifikasi.');
        }

        $judul      = trim($request->judul ?? '');
        $deskripsi  = trim($request->deskripsi ?? '');
        $targetBuku = max(1, (int) ($request->target_buku ?? 1));
        $batasWaktu = $request->batas_waktu ?? '';

        if (empty($judul) || empty($deskripsi) || empty($batasWaktu)) {
            return back()->with('error', 'Judul, deskripsi, dan batas waktu harus diisi.')->withInput();
        }

        $data = [
            'user_id'     => auth()->id(),
            'judul'       => $judul,
            'deskripsi'   => $deskripsi,
            'target_buku' => $targetBuku,
            'batas_waktu' => $batasWaktu,
            'status'      => 'pending',
        ];

        if ($request->hasFile('foto_campaign')) {
            $foto = $this->uploadService->uploadImage($request->file('foto_campaign'), 'campaigns');
            if ($foto) $data['foto_campaign'] = $foto;
        }

        $campaign = CampaignDonasi::create($data);

        LogAktivitas::record(auth()->id(), 'Buat Kampanye', "Membuat kampanye donasi: {$judul}");

        return redirect('/donasi/dashboard')->with('success', 'Kampanye berhasil diajukan! Menunggu persetujuan admin.');
    }

    // ─── MITRA: Konfirmasi Buku Diterima ──────────────
    public function konfirmasiDiterima(Request $request)
    {
        $donasiId = (int) $request->donasi_id;
        $donasi = DonasiBuku::with('campaign')->find($donasiId);

        if (!$donasi || $donasi->campaign->user_id !== auth()->id()) abort(403);

        $dataUpdate = ['status_pengiriman' => 'diterima'];

        if ($request->hasFile('foto_terima')) {
            $foto = $this->uploadService->uploadImage($request->file('foto_terima'), 'donasi_bukti');
            if ($foto) $dataUpdate['foto_terima'] = $foto;
        }

        if ($request->filled('pesan_terima')) {
            $dataUpdate['pesan_terima'] = trim($request->pesan_terima);
        }

        $donasi->update($dataUpdate);

        // Update campaign terkumpul
        $campaign = $donasi->campaign;
        $campaign->increment('terkumpul', $donasi->jumlah);

        // Check if completed
        if ($campaign->terkumpul >= $campaign->target_buku) {
            $campaign->update(['status' => 'completed']);
        }

        // Notify donatur
        Notifikasi::create([
            'user_id'    => $donasi->user_id,
            'tipe'       => 'donasi_diterima',
            'pesan'      => 'Buku "' . $donasi->judul_buku . '" telah diterima oleh ' . $campaign->user->mitraVerification->nama_instansi . '! Terima kasih atas donasi Anda ❤️',
            'url_target' => '/profile?tab=donations',
        ]);

        LogAktivitas::record(auth()->id(), 'Konfirmasi Donasi', "Mengkonfirmasi penerimaan buku '{$donasi->judul_buku}' ({$donasi->jumlah} eks)");

        return back()->with('success', 'Buku berhasil dikonfirmasi diterima!');
    }

    // ─── MITRA: Detail Donasi Masuk ───────────────────
    public function campaignDonations(int $campaignId)
    {
        $campaign = CampaignDonasi::where('user_id', auth()->id())->find($campaignId);
        if (!$campaign) abort(404);

        $donations = $campaign->donasiBuku()
            ->with('user:id,username,foto_profil')
            ->orderByDesc('created_at')
            ->get();

        return view('donasi.campaign-donations', compact('campaign', 'donations'));
    }

    // ─── USER: Laporkan Mitra ─────────────────────────
    public function laporkanMitra(Request $request)
    {
        $mitraId = (int) $request->mitra_id;
        
        \App\Models\LaporanGlobal::create([
            'pelapor_id'      => auth()->id(),
            'tipe_entitas'    => 'mitra',
            'entitas_id'      => $mitraId,
            'alasan'          => trim($request->alasan),
            'detail_tambahan' => trim($request->detail_tambahan),
            'status'          => 'pending',
        ]);

        LogAktivitas::record(auth()->id(), 'Lapor Mitra', "Melaporkan mitra ID: {$mitraId}");

        return back()->with('success', 'Laporan berhasil dikirim. Tim kami akan segera memeriksanya.');
    }
}
