<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenID extends Model
{
    protected $fillable = [
        'uuid',
        'token_id',
        'access_token',
    ];
}
