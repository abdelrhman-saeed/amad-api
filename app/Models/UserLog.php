<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserLog extends Model
{
    protected $connection = 'sqlite';
    protected $fillable   = ['request', 'response', 'user_id', 'token_id'];


    public function user()
    {
        return User::find($this->user_id);
    }

    public function token()
    {
        return TokenID::find($this->token_id);
    }
}
