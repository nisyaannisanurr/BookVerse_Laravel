@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'reports'; @endphp

<div class="d-flex align-center justify-between mb-lg">
    <div>
        <a href="{{ url('/admin/komunitas') }}" class="text-muted text-sm mb-sm" style="display:inline-block;">&larr; Kembali ke Dashboard</a>
        <h2 style="font-family:var(--font-display);">🚨 Moderasi Laporan</h2>
        <p class="text-muted">Komunitas: {{ $community->nama_komunitas }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-md">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error mb-md">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body" style="padding:0;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="border-bottom:1px solid var(--border); background:var(--bg-body);">
                    <th style="padding:1rem;">Tanggal</th>
                    <th style="padding:1rem;">Pelapor</th>
                    <th style="padding:1rem;">Tipe</th>
                    <th style="padding:1rem;">Alasan Laporan</th>
                    <th style="padding:1rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:1rem;">{{ $report->created_at->format('d M Y, H:i') }}</td>
                    <td style="padding:1rem;">{{ $report->pelapor->username }}</td>
                    <td style="padding:1rem;">
                        @if($report->postingan_id)
                            <span class="badge" style="background:var(--primary); color:white; padding:4px 8px; border-radius:4px;">Postingan</span>
                        @elseif($report->komentar_id)
                            <span class="badge" style="background:var(--secondary); color:white; padding:4px 8px; border-radius:4px;">Komentar</span>
                        @endif
                    </td>
                    <td style="padding:1rem;">
                        <strong>Alasan:</strong> {{ $report->alasan }}<br>
                        <div style="background:var(--bg-body); padding:8px; border-radius:4px; margin-top:4px; font-size:0.9rem; border-left:3px solid var(--border);">
                            @if($report->postingan_id && $report->postingan)
                                "{{ Str::limit($report->postingan->konten, 50) }}"
                            @elseif($report->komentar_id && $report->komentar)
                                "{{ Str::limit($report->komentar->konten, 50) }}"
                            @else
                                <span class="text-muted">Konten sudah dihapus</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding:1rem;">
                        @if($report->status === 'pending')
                            <form action="{{ route('admin.komunitas.reports.resolve') }}" method="POST" class="d-flex gap-sm">
                                @csrf
                                <input type="hidden" name="report_id" value="{{ $report->id }}">
                                
                                @if($report->postingan_id && $report->postingan)
                                    <button type="submit" name="action" value="delete_post" class="btn btn-sm" style="background:var(--danger); color:white; border:none;">Hapus Postingan</button>
                                @elseif($report->komentar_id && $report->komentar)
                                    <button type="submit" name="action" value="delete_comment" class="btn btn-sm" style="background:var(--danger); color:white; border:none;">Hapus Komentar</button>
                                @endif
                                
                                <button type="submit" name="action" value="dismiss" class="btn btn-sm btn-outline">Abaikan</button>
                            </form>
                        @else
                            <span class="badge" style="background:{{ $report->status == 'resolved' ? 'var(--success)' : 'var(--text-muted)' }}; color:white; padding:4px 8px; border-radius:4px;">
                                {{ ucfirst($report->status) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:2rem; text-align:center; color:var(--text-muted);">Tidak ada laporan. Komunitas aman! ✨</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
