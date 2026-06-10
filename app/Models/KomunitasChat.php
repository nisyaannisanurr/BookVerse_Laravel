<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomunitasChat extends Model
{
    use HasFactory;

    protected $table = 'komunitas_chats';
    protected $fillable = [
        'komunitas_id',
        'user_id',
        'parent_id',
        'pesan',
        'media_path',
        'media_type',
        'reactions',
    ];

    protected $casts = [
        'media_path' => 'array',
        'media_type' => 'array',
        'reactions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }

    public function parent()
    {
        return $this->belongsTo(KomunitasChat::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(KomunitasChat::class, 'parent_id');
    }
}
