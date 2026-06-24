<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignDonasi extends Model
{
    protected $table = 'campaign_donasis';

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'foto_campaign',
        'target_buku',
        'terkumpul',
        'batas_waktu',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'batas_waktu' => 'date',
        'target_buku' => 'integer',
        'terkumpul'   => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function donasiBuku()
    {
        return $this->hasMany(DonasiBuku::class, 'campaign_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBelumBerakhir($query)
    {
        return $query->where('batas_waktu', '>=', now()->toDateString());
    }

    // Helpers
    public function getProgressPercentAttribute(): int
    {
        if ($this->target_buku <= 0) return 0;
        return min(100, (int) round(($this->terkumpul / $this->target_buku) * 100));
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->terkumpul >= $this->target_buku;
    }

    public function sisaHari(): int
    {
        return max(0, (int) now()->diffInDays($this->batas_waktu, false));
    }
}
