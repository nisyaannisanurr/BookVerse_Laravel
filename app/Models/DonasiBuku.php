<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonasiBuku extends Model
{
    protected $table = 'donasi_bukus';

    protected $fillable = [
        'campaign_id',
        'user_id',
        'no_wa_donatur',
        'judul_buku',
        'jumlah',
        'kondisi',
        'resi_pengiriman',
        'foto_terima',
        'pesan_terima',
        'catatan',
        'status_pengiriman',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function campaign()
    {
        return $this->belongsTo(CampaignDonasi::class, 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helpers
    public function getStatusLabelAttribute(): string
    {
        return match($this->status_pengiriman) {
            'menunggu_dikirim' => 'Menunggu Dikirim',
            'dikirim'          => 'Sedang Dikirim',
            'diterima'         => 'Diterima',
            default            => $this->status_pengiriman,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status_pengiriman) {
            'menunggu_dikirim' => '#d97706',
            'dikirim'          => '#2563eb',
            'diterima'         => '#16a34a',
            default            => '#6b7280',
        };
    }
}
