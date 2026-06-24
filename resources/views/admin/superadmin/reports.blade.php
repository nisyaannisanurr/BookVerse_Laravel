@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'reports';
    use App\Helpers\BookVerseHelper;
@endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">🚩 Pusat Laporan Global</h2>

<div class="card">
    <div class="card-body table-responsive">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 8px;text-align:left;">Pelapor</th>
                    <th style="padding:12px 8px;text-align:left;">Entitas</th>
                    <th style="padding:12px 8px;text-align:left;">Alasan</th>
                    <th style="padding:12px 8px;text-align:left;">Status</th>
                    <th style="padding:12px 8px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                @php
                    $entityName = 'Unknown';
                    $entityLink = '#';
                    
                    if ($r->tipe_entitas === 'komunitas') {
                        $k = \App\Models\Komunitas::find($r->entitas_id);
                        if ($k) {
                            $entityName = $k->nama_komunitas;
                            $entityLink = route('admin.superadmin.communities.detail', $k->id);
                        }
                    } elseif ($r->tipe_entitas === 'preloved') {
                        $p = \App\Models\PrelovedBook::find($r->entitas_id);
                        if ($p) {
                            $entityName = $p->judul_buku;
                            $entityLink = route('admin.superadmin.preloved.detail', $p->id);
                        }
                    } elseif ($r->tipe_entitas === 'user') {
                        $u = \App\Models\User::find($r->entitas_id);
                        if ($u) {
                            $entityName = $u->username;
                            $entityLink = route('admin.superadmin.users.detail', $u->id);
                        }
                    } elseif ($r->tipe_entitas === 'mitra') {
                        $m = \App\Models\MitraVerification::where('user_id', $r->entitas_id)->first();
                        if ($m) {
                            $entityName = $m->nama_instansi;
                            $entityLink = url('/admin/superadmin/mitra/' . $m->id);
                        }
                    } elseif ($r->tipe_entitas === 'postingan') {
                        $post = \App\Models\PostinganKomunitas::find($r->entitas_id);
                        if ($post) {
                            $entityName = \Illuminate\Support\Str::limit($post->isi_postingan, 30);
                            $entityLink = url('/community/post/' . $post->id);
                        }
                    }
                @endphp
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 8px;">{{ $r->pelapor->username ?? 'Unknown' }}</td>
                    <td style="padding:12px 8px;">
                        <span class="badge badge-info" style="margin-bottom:6px; display:inline-block;">{{ strtoupper($r->tipe_entitas) }}</span><br>
                        <a href="{{ $entityLink }}" target="_blank" style="color:#2563eb; font-weight:600; text-decoration:none;">
                            {{ $entityName }} <span style="font-size:0.8rem;">↗</span>
                        </a>
                        <div style="font-size:0.75rem; color:#64748b; margin-top:4px;">ID: {{ $r->entitas_id }}</div>
                    </td>
                    <td style="padding:12px 8px; max-width:250px;">
                        <strong style="color:#0f172a;">{{ $r->alasan }}</strong><br>
                        <small style="color:#64748b; display:block; margin-top:4px;">{{ BookVerseHelper::truncate($r->detail_tambahan, 60) }}</small>
                    </td>
                    <td style="padding:12px 8px;">
                        @if($r->status === 'pending')
                            <span class="badge" style="background:#fef08a;color:#854d0e;">Pending</span>
                        @elseif($r->status === 'diproses')
                            <span class="badge" style="background:#bfdbfe;color:#1e40af;">Diproses</span>
                        @elseif($r->status === 'selesai')
                            <span class="badge" style="background:#dcfce7;color:#16a34a;">Selesai</span>
                        @else
                            <span class="badge" style="background:#fee2e2;color:#dc2626;">Ditolak</span>
                        @endif
                    </td>
                    <td style="padding:12px 8px;text-align:center;">
                        <form method="POST" action="{{ route('admin.superadmin.reports.resolve') }}" style="display:flex; flex-direction:column; gap:6px; align-items:center;">
                            @csrf
                            <input type="hidden" name="report_id" value="{{ $r->id }}">
                            <select name="status" class="form-control" style="font-size:0.85rem; padding:6px; width: 120px; border-radius:8px;" onchange="this.form.submit()">
                                <option value="pending" {{ $r->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="diproses" {{ $r->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="selesai" {{ $r->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditolak" {{ $r->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            <span style="font-size:0.65rem; color:#dc2626; max-width: 120px; line-height: 1.2;">
                                *Pilih 'Selesai' untuk otomatis Banned/Nonaktifkan entitas ini.
                            </span>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:30px;text-align:center;color:#64748b;">Belum ada laporan yang masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
