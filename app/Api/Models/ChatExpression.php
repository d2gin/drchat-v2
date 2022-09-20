<?php

namespace App\Api\Models;

use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatExpression extends Model
{
    use HasFactory;
    use TimeFormat;

    protected $table = 'icy8_chat_expression';
}
