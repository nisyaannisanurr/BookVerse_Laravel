@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'preloved';
    use App\Helpers\BookVerseHelper;
@endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">🏷️ Kelola Preloved</h2>

<div class="card">
    <div class="card-body" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 8px;text-align:left;">Buku</th>
                    <th style="padding:12px 8px;text-align:left;">Penjual</th>
                    <th style="padding:12px 8px;text-align:right;">Harga</th>
                    <th style="padding:12px 8px;text-align:center;">Status</th>
                    <th style="padding:12px 8px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listings as $item)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:8px;font-weight:600;">{{ $item->judul_buku }}</td>
                    <td style="padding:8px;">{{ $item->user->username ?? 'Unknown' }}</td>
                    <td style="padding:8px;text-align:right;">{{ BookVerseHelper::formatRupiah($item->harga) }}</td>
                    <td style="padding:8px;text-align:center;">{!! BookVerseHelper::statusBadge($item->status_buku) !!}</td>
                    <td style="padding:8px;text-align:center;">
                        <div class="d-flex gap-sm justify-center">
                            <form method="POST" action="{{ url('/admin/superadmin/preloved/suspend') }}" class="inline-form">
                                @csrf
                                <input type="hidden" name="listing_id" value="{{ $item->id }}">
                                <button type="submit" class="btn btn-outline btn-sm">
                                    {{ $item->status_buku === 'ditangguhkan' ? '▶️' : '⏸️' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ url('/admin/superadmin/preloved/delete') }}" class="inline-form" onsubmit="return confirm('Hapus listing ini?')">
                                @csrf
                                <input type="hidden" name="listing_id" value="{{ $item->id }}">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
