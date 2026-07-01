<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookyChat extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'parent_id', 'is_bot', 'pesan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(BookyChat::class, 'parent_id');
    }
}
