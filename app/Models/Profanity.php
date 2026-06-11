<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profanity extends Model
{
    protected $table = 'profanities';
    
    protected $fillable = ['kata'];
}
