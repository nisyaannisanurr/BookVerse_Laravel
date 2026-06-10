<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrelovedBook extends Model
{
    protected $table = 'preloved_books';
    const UPDATED_AT = null; // tabel tidak punya kolom updated_at

    protected $fillable = [
        'user_id',
        'judul_buku',
        'kondisi_buku',
        'harga',
        'foto_buku',
        'deskripsi',
        'no_wa',
        'status_buku',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeTersedia($query)
    {
        return $query->where('status_buku', 'tersedia');
    }

    public function scopeFiltered($query, array $filters)
    {
        if (!empty($filters['kondisi'])) {
            $query->where('kondisi_buku', $filters['kondisi']);
        }
        if (!empty($filters['min_harga'])) {
            $query->where('harga', '>=', (float)$filters['min_harga']);
        }
        if (!empty($filters['max_harga'])) {
            $query->where('harga', '<=', (float)$filters['max_harga']);
        }
        if (!empty($filters['search'])) {
            $like = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($like) {
                $q->where('judul_buku', 'LIKE', $like)
                  ->orWhere('deskripsi', 'LIKE', $like);
            });
        }
        return $query;
    }
}
