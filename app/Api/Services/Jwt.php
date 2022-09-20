<?php

namespace App\Api\Services;

use Firebase\JWT\JWT as JWTUtil;
use Firebase\JWT\Key;
use Illuminate\Support\Env;

class Jwt
{
    public function create($uid, $pwdHash, $expired = 86400)
    {
        $request = request();
        $app_key = Env::get('APP_KEY', 'icy8');
        $time    = time();
        $payload = [
            'sub' => $request->getHttpHost(),// 面向用户
            'iss' => $request->getHttpHost(),//签发者
            'iat' => $time,//签发时间
            'nbf' => $time,// 该时间之前无法验证该token
            'jti' => ['uid' => $uid, 'hash' => $pwdHash],// 标识
            'exp' => $time + $expired,//过期时间
        ];
        return JWTUtil::encode($payload, $app_key, 'HS256');
    }

    public function decode($token)
    {
        $app_key = Env::get('APP_KEY', 'icy8');
        $decode  = JWTUtil::decode($token, new Key($app_key, 'HS256'));
        $jti     = $decode->jti ?? new \stdClass();
        return collect($jti)->toArray();
    }
}