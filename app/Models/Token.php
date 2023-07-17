<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'tokens';

    protected $attributes = [
        'is_used' => 0
    ];

    protected $fillable = [
        'id',
        'token',
        'is_used'
    ];
}
