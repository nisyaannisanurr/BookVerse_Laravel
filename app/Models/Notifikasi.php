<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    const UPDATED_AT = null; // tabel tidak punya kolom updated_at

    protected $fillable = [
        'user_id',
        'tipe',
        'pesan',
        'url_target',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }
}
