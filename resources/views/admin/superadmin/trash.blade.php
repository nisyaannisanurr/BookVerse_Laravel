@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'trash';
    use App\Helpers\BookVerseHelper;
@endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">🗑️ Tong Sampah (Recycle Bin)</h2>

<div class="tabs mb-md" style="display:flex; gap:10px; border-bottom:1px solid var(--border); padding-bottom:10px;">
    <button class="btn btn-ghost active" onclick="showTab('tab-books')" id="btn-books">Buku Terhapus</button>
    <button class="btn btn-ghost" onclick="showTab('tab-posts')" id="btn-posts">Postingan Komunitas</button>
</div>

<!-- TAB BOOKS -->
<div id="tab-books" class="tab-content" style="display:block;">
    <div class="card">
        <div class="card-body" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border);">
                        <th style="padding:12px 8px;text-align:left;">Judul Buku</th>
                        <th style="padding:12px 8px;text-align:left;">Dihapus Pada</th>
                        <th style="padding:12px 8px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $b)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:8px;font-weight:600;">{{ $b->judul }}</td>
                        <td style="padding:8px;">{{ BookVerseHelper::formatTanggal($b->deleted_at) }}</td>
                        <td style="padding:8px;text-align:center;">
                            <form method="POST" action="{{ route('admin.superadmin.trash.book.restore') }}" class="inline-form">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $b->id }}">
                                <button type="submit" class="btn btn-sm btn-outline" style="color:var(--success); border-color:var(--success);">♻️ Pulihkan</button>
                            </form>
                            <form method="POST" action="{{ route('admin.superadmin.trash.book.delete') }}" class="inline-form" onsubmit="return confirm('Hapus buku ini PERMANEN beserta gambar covernya?');">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $b->id }}">
                                <button type="submit" class="btn btn-sm" style="background:var(--danger); color:white;">🗑️ Hapus Permanen</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding:20px;text-align:center;color:var(--text-muted);">Tidak ada buku yang terhapus.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB POSTS -->
<div id="tab-posts" class="tab-content" style="display:none;">
    <div class="card">
        <div class="card-body" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border);">
                        <th style="padding:12px 8px;text-align:left;">Konten Singkat</th>
                        <th style="padding:12px 8px;text-align:left;">Pembuat</th>
                        <th style="padding:12px 8px;text-align:left;">Komunitas</th>
                        <th style="padding:12px 8px;text-align:left;">Dihapus Pada</th>
                        <th style="padding:12px 8px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $p)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:8px;">{{ BookVerseHelper::truncate($p->konten, 50) }}</td>
                        <td style="padding:8px;">{{ $p->user->username ?? 'Unknown' }}</td>
                        <td style="padding:8px;">{{ $p->komunitas->nama_komunitas ?? 'Unknown' }}</td>
                        <td style="padding:8px;">{{ BookVerseHelper::formatTanggal($p->deleted_at) }}</td>
                        <td style="padding:8px;text-align:center;">
                            <form method="POST" action="{{ route('admin.superadmin.trash.post.restore') }}" class="inline-form">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $p->id }}">
                                <button type="submit" class="btn btn-sm btn-outline" style="color:var(--success); border-color:var(--success);">♻️ Pulihkan</button>
                            </form>
                            <form method="POST" action="{{ route('admin.superadmin.trash.post.delete') }}" class="inline-form" onsubmit="return confirm('Hapus postingan ini PERMANEN beserta gambar attachmentnya?');">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $p->id }}">
                                <button type="submit" class="btn btn-sm" style="background:var(--danger); color:white;">🗑️ Hapus Permanen</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:20px;text-align:center;color:var(--text-muted);">Tidak ada postingan yang terhapus.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.getElementById(tabId).style.display = 'block';
        
        document.querySelectorAll('.tabs button').forEach(el => {
            el.style.borderBottom = 'none';
            el.style.fontWeight = 'normal';
        });
        document.getElementById('btn-' + tabId.split('-')[1]).style.borderBottom = '2px solid var(--primary)';
        document.getElementById('btn-' + tabId.split('-')[1]).style.fontWeight = 'bold';
    }
    
    // Init state
    document.getElementById('btn-books').style.borderBottom = '2px solid var(--primary)';
    document.getElementById('btn-books').style.fontWeight = 'bold';
</script>
@endsection
