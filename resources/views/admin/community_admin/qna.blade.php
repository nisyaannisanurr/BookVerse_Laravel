@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'qna'; @endphp

<div class="page-header">
    <div>
        <h1>Sesi Q&A Interaktif</h1>
        <p>Buka sesi tanya jawab dengan penulis, narasumber, atau admin untuk <b>{{ $community->nama_komunitas }}</b>.</p>
    </div>
</div>

<div class="stat-card" style="display: block; margin-bottom: 32px;">
    <h3 style="margin-bottom: 16px; font-size: 1.1rem; color: #1a1030;">Buka Sesi Q&A Baru</h3>
    <form action="{{ route('admin.komunitas.qna.create', $community->id) }}" method="POST" style="display: flex; gap: 16px; align-items: flex-end;">
        @csrf
        <div class="form-group" style="flex: 2;">
            <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Judul Sesi (Topik Pembahasan)</label>
            <input type="text" name="judul" required placeholder="Contoh: Q&A Bersama Tere Liye" style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
        </div>
        <div class="form-group" style="flex: 1;">
            <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Nama Narasumber</label>
            <input type="text" name="narasumber" placeholder="Opsional..." style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
        </div>
        <button type="submit" class="btn btn-primary" style="background: #8b5cf6; border-color: #8b5cf6; height: 42px;">Mulai Sesi</button>
    </form>
</div>

<h3 style="margin-bottom: 16px; font-size: 1.2rem; color: #1a1030;">Pertanyaan dari Anggota</h3>
<div style="display: flex; flex-direction: column; gap: 24px;">
    @foreach($qnas as $qna)
    <div style="background: white; border: 1px solid #e8e0f0; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
            <div>
                <h4 style="font-size: 1.2rem; color: #1a1030; margin-bottom: 4px;">{{ $qna->judul }}</h4>
                <div style="font-size: 0.85rem; color: #6b7280;">Narasumber: <b>{{ $qna->narasumber ?? 'Admin Komunitas' }}</b> &bull; Status: <span style="color: {{ $qna->status === 'open' ? '#10b981' : '#ef4444' }}; font-weight: 600;">{{ strtoupper($qna->status) }}</span></div>
            </div>
            <div style="background: rgba(139, 92, 246, 0.1); padding: 8px 16px; border-radius: 8px; color: #8b5cf6; font-weight: 600; font-size: 0.85rem;">
                {{ $qna->pertanyaan->count() }} Pertanyaan
            </div>
        </div>

        <div style="border-top: 1px solid #e8e0f0; padding-top: 16px; display: flex; flex-direction: column; gap: 16px;">
            @foreach($qna->pertanyaan as $tanya)
            <div style="background: #f8f9fc; padding: 16px; border-radius: 8px; border-left: 4px solid {{ $tanya->jawaban ? '#10b981' : '#f59e0b' }};">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <div style="width: 24px; height: 24px; background: #e0e7ff; color: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem;">
                        {{ strtoupper(substr($tanya->user->username, 0, 1)) }}
                    </div>
                    <span style="font-size: 0.85rem; font-weight: 600; color: #374151;">{{ $tanya->user->username }}</span>
                    <span style="font-size: 0.75rem; color: #9ca3af;">{{ $tanya->created_at->diffForHumans() }}</span>
                </div>
                <p style="font-size: 0.95rem; color: #1f2937; margin-bottom: {{ $tanya->jawaban ? '12px' : '16px' }};">"{{ $tanya->pertanyaan }}"</p>
                
                @if($tanya->jawaban)
                <div style="background: white; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 0.75rem; color: #8b5cf6; font-weight: 700; margin-bottom: 4px; text-transform: uppercase;">Jawaban:</div>
                    <p style="font-size: 0.9rem; color: #4b5563;">{{ $tanya->jawaban }}</p>
                </div>
                @else
                <form action="{{ route('admin.komunitas.qna.answer', $community->id) }}" method="POST" style="display: flex; gap: 8px;">
                    @csrf
                    <input type="hidden" name="pertanyaan_id" value="{{ $tanya->id }}">
                    <input type="text" name="jawaban" required placeholder="Tulis jawaban..." style="flex: 1; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <button type="submit" class="btn btn-primary btn-sm" style="background: #10b981; border-color: #10b981;">Jawab</button>
                </form>
                @endif
            </div>
            @endforeach
            
            @if($qna->pertanyaan->isEmpty())
            <div style="text-align: center; color: #9ca3af; font-size: 0.85rem; padding: 16px;">Belum ada pertanyaan.</div>
            @endif
        </div>
    </div>
    @endforeach

    @if($qnas->isEmpty())
    <div style="background: white; padding: 32px; border-radius: 12px; text-align: center; color: #9b90a8; border: 1px dashed #e8e0f0;">
        Belum ada sesi Q&A yang dibuat.
    </div>
    @endif
</div>

@endsection
