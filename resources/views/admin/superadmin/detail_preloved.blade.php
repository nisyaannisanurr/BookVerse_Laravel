@extends('layouts.admin')

@section('styles')
<style>
    .preloved-header {
        background: white; border-radius: 24px; padding: 32px; margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f3f4f6;
        display: flex; gap: 32px; align-items: stretch;
    }
    .p-cover {
        width: 280px; height: 350px; object-fit: cover; border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); flex-shrink: 0; border: 1px solid #e2e8f0;
    }
    .p-info { flex: 1; display: flex; flex-direction: column; }
    .p-badge {
        display: inline-block; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700;
        margin-bottom: 16px; align-self: flex-start; text-transform: uppercase;
    }
    .p-title { font-size: 2rem; font-weight: 800; font-family: 'Playfair Display', serif; color: #0f172a; margin: 0 0 12px; }
    .p-price { font-size: 1.8rem; font-weight: 800; color: #16a34a; margin: 0 0 20px; }
    
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
    .info-item { background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; }
    .info-label { font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
    .info-value { font-size: 1.05rem; font-weight: 700; color: #1e293b; }
    
    .p-desc { font-size: 0.95rem; line-height: 1.6; color: #475569; margin-bottom: 24px; flex: 1; }
    
    .action-panel {
        background: #fffbfa; border: 1px solid #fee2e2; border-radius: 20px; padding: 24px;
        display: flex; justify-content: space-between; align-items: center; margin-top: auto;
    }
    .btn-suspend {
        background: #ef4444; color: white; border: none; padding: 12px 24px;
        border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-suspend:hover { background: #dc2626; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2); }
    .btn-restore { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
    
    .section-card { background: white; border-radius: 24px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f3f4f6; }
    .section-title { font-size: 1.3rem; font-weight: 800; color: #111827; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
    
    .seller-card { display: flex; align-items: center; gap: 20px; }
    .s-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; background: #f1f5f9; border: 2px solid #e2e8f0; }
    .s-info h4 { font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 0 0 4px; }
    .s-info p { margin: 0; color: #64748b; font-size: 0.9rem; }
    .btn-view-seller {
        display: inline-block; margin-top: 12px; padding: 6px 16px; border-radius: 50px;
        font-size: 0.8rem; font-weight: 600; background: #eff6ff; color: #2563eb; text-decoration: none; border: 1px solid #bfdbfe;
    }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; margin-bottom: 20px; transition: 0.2s; }
    .btn-back:hover { color: #0f172a; }
</style>
@endsection

@section('content')

<a href="{{ url('/admin/superadmin/reports') }}" class="btn-back">← Kembali</a>

<div class="preloved-header">
    <img src="{{ asset('uploads/preloved/' . $book->foto_buku) }}" alt="Cover" class="p-cover">
    
    <div class="p-info">
        @if($book->status_buku == 'tersedia')
            <span class="p-badge" style="background:#dcfce7; color:#166534;">Tersedia</span>
        @elseif($book->status_buku == 'terjual')
            <span class="p-badge" style="background:#e0e7ff; color:#3730a3;">Terjual</span>
        @else
            <span class="p-badge" style="background:#fee2e2; color:#991b1b;">Ditangguhkan</span>
        @endif
        
        <h1 class="p-title">{{ $book->judul_buku }}</h1>
        <div class="p-price">Rp {{ number_format($book->harga, 0, ',', '.') }}</div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Kondisi</div>
                <div class="info-value">{{ ucfirst($book->kondisi) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tanggal Posting</div>
                <div class="info-value">{{ $book->created_at->format('d M Y') }}</div>
            </div>
        </div>
        
        <div class="p-desc">
            <strong>Deskripsi:</strong><br>
            {{ $book->deskripsi }}
        </div>
        
        <div class="action-panel">
            <div>
                <strong style="display:block; color:#7f1d1d; margin-bottom:4px;">Tindakan Admin</strong>
                <span style="font-size:0.85rem; color:#991b1b;">Tangguhkan listing ini jika melanggar aturan atau merupakan penipuan.</span>
            </div>
            <form method="POST" action="{{ route('admin.superadmin.preloved.suspend') }}">
                @csrf
                <input type="hidden" name="listing_id" value="{{ $book->id }}">
                @if($book->status_buku !== 'ditangguhkan')
                    <button type="submit" class="btn-suspend" onclick="return confirm('Yakin ingin menangguhkan buku ini agar tidak tampil di marketplace?')">
                        🚫 Tangguhkan Buku
                    </button>
                @else
                    <button type="submit" class="btn-restore" onclick="return confirm('Yakin ingin memulihkan buku ini?')">
                        ✅ Pulihkan Listing
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="section-card">
    <h2 class="section-title">👤 Informasi Penjual</h2>
    <div class="seller-card">
        <img src="{{ $book->penjual && $book->penjual->foto_profil ? asset('uploads/profiles/' . $book->penjual->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($book->penjual->username ?? 'Penjual') }}" class="s-avatar" alt="">
        <div class="s-info">
            <h4>{{ $book->penjual->username ?? 'Unknown' }}</h4>
            <p>Bergabung sejak: {{ $book->penjual ? $book->penjual->created_at->format('d M Y') : '-' }}</p>
            @if($book->penjual)
                <a href="{{ url('/admin/superadmin/users/detail/' . $book->penjual->id) }}" class="btn-view-seller">Lihat Profil Lengkap User</a>
            @endif
        </div>
    </div>
</div>

@endsection
