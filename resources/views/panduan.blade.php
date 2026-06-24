@extends('layouts.main')
@section('title', 'Panduan & FAQ — BookVerse')

@section('content')
<style>
    /* ── HERO SECTION ── */
    .faq-hero {
        background: var(--gradient-light);
        border-radius: 24px;
        padding: 60px 40px;
        text-align: center;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }
    .faq-hero::before {
        content: '';
        position: absolute;
        top: -50px; left: -50px;
        width: 150px; height: 150px;
        background: rgba(124, 58, 237, 0.05);
        border-radius: 50%;
    }
    .faq-hero::after {
        content: '';
        position: absolute;
        bottom: -50px; right: -50px;
        width: 200px; height: 200px;
        background: rgba(124, 58, 237, 0.05);
        border-radius: 50%;
    }
    .faq-hero h1 {
        font-family: var(--font-display);
        font-size: 2.8rem;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 16px;
        position: relative;
        z-index: 2;
    }
    .faq-hero p {
        color: var(--text-secondary);
        font-size: 1.15rem;
        max-width: 600px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        line-height: 1.6;
    }

    /* ── LAYOUT & TABS ── */
    .faq-layout {
        display: flex;
        gap: 40px;
        max-width: 1000px;
        margin: 0 auto;
        align-items: flex-start;
    }
    .faq-sidebar {
        width: 250px;
        flex-shrink: 0;
        position: sticky;
        top: 100px;
    }
    .faq-main {
        flex: 1;
        min-width: 0;
    }

    .category-link {
        display: block;
        padding: 12px 20px;
        border-radius: 12px;
        color: var(--text-secondary);
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        background: transparent;
    }
    .category-link:hover {
        background: var(--bg-surface);
        color: var(--primary);
    }
    .category-link.active {
        background: var(--primary-light);
        color: var(--primary);
        border-left-color: var(--primary);
    }

    /* ── ACCORDION ITEMS ── */
    .faq-group {
        display: none;
        animation: fadeIn 0.4s ease;
    }
    .faq-group.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .faq-group-title {
        font-family: var(--font-display);
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .faq-item {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        margin-bottom: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .faq-item:hover {
        border-color: rgba(124, 58, 237, 0.3);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.05);
    }
    .faq-question {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--text);
        user-select: none;
    }
    .faq-icon {
        font-size: 1rem;
        color: var(--primary);
        transition: transform 0.3s ease;
        background: var(--primary-light);
        width: 30px; height: 30px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
    }
    .faq-answer {
        padding: 0 24px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease, opacity 0.3s ease;
        color: var(--text-secondary);
        line-height: 1.7;
        font-size: 1rem;
        opacity: 0;
    }
    
    .faq-item.active {
        border-color: var(--primary);
        box-shadow: 0 4px 15px rgba(124, 58, 237, 0.1);
    }
    .faq-item.active .faq-question {
        color: var(--primary);
    }
    .faq-item.active .faq-icon {
        transform: rotate(180deg);
        background: var(--primary);
        color: white;
    }
    .faq-item.active .faq-answer {
        padding: 0 24px 24px;
        max-height: 1000px;
        opacity: 1;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .faq-layout { flex-direction: column; gap: 24px; }
        .faq-sidebar { width: 100%; position: static; display: flex; overflow-x: auto; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
        .category-link { flex-shrink: 0; margin-bottom: 0; border-left: none; border-bottom: 3px solid transparent; border-radius: 8px; margin-right: 8px; }
        .category-link.active { border-left: none; border-bottom-color: var(--primary); background: transparent; }
    }
</style>

<div class="container fade-in-up">
    
    <div class="faq-hero">
        <h1>Pusat Bantuan BookVerse 💡</h1>
        <p>Temukan panduan lengkap, aturan, dan tips seputar fitur Komunitas, Donasi, Preloved, dan lainnya di ekosistem BookVerse.</p>
    </div>

    @if($groupedPanduans->count() > 0)
    <div class="faq-layout">
        
        {{-- SIDEBAR CATEGORIES --}}
        <div class="faq-sidebar">
            <h3 style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); margin-bottom:12px; padding-left:20px;">Kategori</h3>
            @foreach($groupedPanduans->keys() as $index => $kategori)
                <a href="javascript:void(0)" 
                   class="category-link {{ $index === 0 ? 'active' : '' }}" 
                   onclick="showCategory('cat-{{ Str::slug($kategori) }}', this)">
                   {{ $kategori }}
                </a>
            @endforeach
        </div>

        {{-- MAIN FAQ ACCORDIONS --}}
        <div class="faq-main">
            @foreach($groupedPanduans as $kategori => $panduans)
                <div id="cat-{{ Str::slug($kategori) }}" class="faq-group {{ $loop->first ? 'active' : '' }}">
                    <h2 class="faq-group-title">
                        @if(Str::contains(strtolower($kategori), 'akun')) 🛡️
                        @elseif(Str::contains(strtolower($kategori), 'komunitas')) 👥
                        @elseif(Str::contains(strtolower($kategori), 'donasi')) 🎁
                        @elseif(Str::contains(strtolower($kategori), 'preloved')) 🏷️
                        @elseif(Str::contains(strtolower($kategori), 'rekomendasi')) ✨
                        @else 📌
                        @endif
                        {{ $kategori }}
                    </h2>
                    
                    @foreach($panduans as $panduan)
                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                <span>{{ $panduan->pertanyaan }}</span>
                                <span class="faq-icon">▼</span>
                            </div>
                            <div class="faq-answer">
                                {!! nl2br(e($panduan->jawaban)) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>
    @else
        <div style="text-align:center; padding: 60px; background:var(--bg-card); border-radius:16px; border:1px dashed var(--border);">
            <div style="font-size:3rem; margin-bottom:16px;">📂</div>
            <h3>Belum Ada Panduan</h3>
            <p style="color:var(--text-secondary);">Superadmin akan segera memperbarui informasi panduan di sini.</p>
        </div>
    @endif

</div>

<script>
function toggleFaq(element) {
    const item = element.parentElement;
    
    // Auto-close others in the same group
    const group = item.closest('.faq-group');
    group.querySelectorAll('.faq-item').forEach(el => {
        if (el !== item) el.classList.remove('active');
    });

    item.classList.toggle('active');
}

function showCategory(groupId, btnElement) {
    // Hide all groups
    document.querySelectorAll('.faq-group').forEach(el => {
        el.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.category-link').forEach(el => {
        el.classList.remove('active');
    });
    
    // Show selected group
    document.getElementById(groupId).classList.add('active');
    
    // Set active button
    btnElement.classList.add('active');
}
</script>
@endsection
