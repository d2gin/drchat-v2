<?php

namespace App\Api\Models;

use App\Concerns\TimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExpression extends Model
{
    use HasFactory;
    use TimeFormat;
    protected $table='icy8_user_expression';
}
