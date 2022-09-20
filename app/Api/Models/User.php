<?php

namespace App\Api\Models;

use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TimeFormat;

    const TOKEN_CACHE_KEY = 'drchat_token_';
    const TOKEN_EXPIRE    = 86400;

    protected        $table     = 'icy8_user';
    protected static $unguarded = true;

    public function expression()
    {
        return $this->hasMany(UserExpression::class, 'user_id', 'id')->orderBy('sort');
    }

    public function conversation()
    {
        return $this->hasMany(ChatConversation::class, 'user_id', 'id')->orderByDesc('updated_at');
    }

    public function passwordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function passwordVerify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function isPassword($password)
    {
        return preg_match('/^[\w\-.?]{6,12}$/is', $password);
    }
}
