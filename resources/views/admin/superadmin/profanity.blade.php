@extends('layouts.admin')
@section('title', 'Filter Kata Kasar - Superadmin BookVerse')
@section('content')
@php $currentAdminPage = 'profanity'; @endphp

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:30px;">
    <div>
        <h1 style="color:#1a1030; font-size:1.8rem; font-weight:700; font-family:'Playfair Display', serif;">Filter Kata Kasar</h1>
        <p style="color:#9b90a8; font-size:0.85rem; margin-top:4px;">Kelola daftar kata yang akan disensor (***) secara otomatis pada postingan dan komentar.</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:300px 1fr; gap:30px;">
    <!-- Form Tambah Kata -->
    <div>
        <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.03); border:1px solid #e8e0f0; position:sticky; top:90px;">
            <h3 style="font-size:1.1rem; color:#1a1030; font-weight:700; margin-bottom:15px;">Tambah Kata</h3>
            <p style="font-size:0.8rem; color:#9b90a8; margin-bottom:20px;">Masukkan kata kasar baru yang ingin diblokir.</p>

            <form method="POST" action="{{ route('admin.superadmin.profanity.add') }}">
                @csrf
                <div style="margin-bottom:15px;">
                    <input type="text" name="kata" required placeholder="Contoh: jelek" 
                           style="width:100%; padding:12px 15px; border-radius:10px; border:1px solid #e8e0f0; background:#f9f8fc; font-family:inherit; font-size:0.9rem;"
                           autocomplete="off">
                    @error('kata')
                        <div style="color:var(--danger); font-size:0.75rem; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; border-radius:10px;">Tambah ke Filter</button>
            </form>
        </div>
    </div>

    <!-- Daftar Kata -->
    <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.03); border:1px solid #e8e0f0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="font-size:1.1rem; color:#1a1030; font-weight:700;">Daftar Kata Tersensor</h3>
            <div style="font-size:0.8rem; color:#9b90a8; background:#f0eef8; padding:5px 12px; border-radius:20px;">
                Total: {{ $words->total() }} kata
            </div>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:12px;">
            @forelse($words as $word)
                <div style="display:flex; align-items:center; background:#fdf2f8; border:1px solid #fbcfe8; padding:6px 14px; border-radius:30px; gap:8px; transition:0.2s;" onmouseover="this.style.background='#fce7f3'" onmouseout="this.style.background='#fdf2f8'">
                    <span style="color:#be185d; font-weight:600; font-size:0.85rem;">{{ $word->kata }}</span>
                    <form method="POST" action="{{ route('admin.superadmin.profanity.delete') }}" style="display:inline;" onsubmit="return confirm('Hapus kata \'{{ $word->kata }}\' dari daftar sensor?');">
                        @csrf
                        <input type="hidden" name="word_id" value="{{ $word->id }}">
                        <button type="submit" style="background:none; border:none; cursor:pointer; color:#f43f5e; font-size:0.9rem; padding:0; display:flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%;" onmouseover="this.style.background='rgba(225,29,72,0.1)'" onmouseout="this.style.background='none'">
                            &times;
                        </button>
                    </form>
                </div>
            @empty
                <div style="padding:30px; text-align:center; width:100%; color:#9b90a8; font-size:0.9rem; font-style:italic;">
                    Belum ada kata yang dimasukkan.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div style="margin-top:30px;">
            {{ $words->links() }}
        </div>
    </div>
</div>
@endsection
