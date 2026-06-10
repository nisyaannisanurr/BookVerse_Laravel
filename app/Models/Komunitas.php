<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komunitas extends Model
{
    protected $table = 'komunitas';

    // Tabel komunitas tidak punya kolom updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'nama_komunitas',
        'deskripsi',
        'peraturan',
        'banner_komunitas',
        'tema_warna',
        'logo_komunitas',
        'creator_id',
        'status',
        'catatan_penolakan',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->hasMany(AnggotaKomunitas::class, 'komunitas_id');
    }

    public function lencana()
    {
        return $this->hasMany(LencanaKomunitas::class, 'komunitas_id');
    }

    public function rakBuku()
    {
        return $this->hasMany(RakBukuKomunitas::class, 'komunitas_id');
    }

    public function tantangan()
    {
        return $this->hasMany(TantanganMembaca::class, 'komunitas_id');
    }

    public function qna()
    {
        return $this->hasMany(QnaKomunitas::class, 'komunitas_id');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaKomunitas::class, 'komunitas_id');
    }

    public function postingan()
    {
        return $this->hasMany(PostinganKomunitas::class, 'komunitas_id');
    }

    public function laporan()
    {
        return $this->hasMany(LaporanKomunitas::class, 'komunitas_id');
    }

    public function event()
    {
        return $this->hasMany(EventKomunitas::class, 'komunitas_id');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Helpers
    public function memberCount(): int
    {
        return $this->anggota()->where('status', 'approved')->count();
    }

    public function pendingCount(): int
    {
        return $this->anggota()->where('status', 'pending')->count();
    }

    public static function withMemberCount()
    {
        return self::selectRaw('komunitas.*, (SELECT COUNT(*) FROM anggota_komunitas WHERE komunitas_id = komunitas.id AND status = "approved") as member_count')
            ->with('creator:id,username');
    }
}
