<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraVerification extends Model
{
    protected $table = 'mitra_verifications';

    protected $fillable = [
        'user_id',
        'instansi_daerah_id',
        'nama_instansi',
        'kategori',
        'alamat_lengkap',
        'link_maps',
        'nama_penanggung_jawab',
        'no_telepon',
        'file_ktp',
        'file_legalitas',
        'foto_bangunan',
        'foto_kegiatan',
        'status_verifikasi',
        'catatan_admin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function instansiDaerah()
    {
        return $this->belongsTo(InstansiDaerah::class, 'instansi_daerah_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status_verifikasi', 'approved');
    }

    public function isApproved(): bool
    {
        return $this->status_verifikasi === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status_verifikasi === 'pending';
    }
}
