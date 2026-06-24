<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostinganKomunitas extends Model
{
    use SoftDeletes;

    protected $table = 'postingan_komunitas';
    const UPDATED_AT = null; // tabel tidak punya kolom updated_at

    protected $fillable = [
        'komunitas_id',
        'user_id',
        'judul',
        'konten',
        'gambar',
        'is_pinned',
        'tantangan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }

    public function tantangan()
    {
        return $this->belongsTo(TantanganMembaca::class, 'tantangan_id');
    }

    public function komentar()
    {
        return $this->hasMany(KomentarPostingan::class, 'postingan_id');
    }

    public function likes()
    {
        return $this->hasMany(PostinganLike::class, 'postingan_id');
    }

    public function commentCount(): int
    {
        return $this->komentar()->count();
    }

    // Scopes
    public static function getTrending(int $limit = 5, ?int $userId = null)
    {
        $query = self::selectRaw('postingan_komunitas.*, (SELECT COUNT(*) FROM komentar_postingan WHERE postingan_id = postingan_komunitas.id) as comment_count')
            ->join('komunitas', 'komunitas.id', '=', 'postingan_komunitas.komunitas_id')
            ->join('users', 'users.id', '=', 'postingan_komunitas.user_id')
            ->where('komunitas.status', 'aktif')
            ->with(['user:id,username,foto_profil', 'komunitas:id,nama_komunitas']);

        if ($userId) {
            $query->join('anggota_komunitas', function ($join) use ($userId) {
                $join->on('anggota_komunitas.komunitas_id', '=', 'komunitas.id')
                    ->where('anggota_komunitas.user_id', '=', $userId)
                    ->where('anggota_komunitas.status', '=', 'approved');
            });
        }

        return $query->orderByDesc('postingan_komunitas.created_at')
            ->limit($limit)
            ->get();
    }
}
