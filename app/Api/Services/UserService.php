<?php

namespace App\Api\Services;

use App\Api\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class UserService
{
    /**
     * 用户登录
     * @param $username
     * @param $password
     * @return bool|string
     */
    public function login($username, $password)
    {
        $jwtService = new Jwt();
        $userModel  = new User();
        $user       = $userModel->newQuery()->where('username', $username)->first();
        if (!$user || !$userModel->passwordVerify($password, $user->password)) {
            return false;
        }
        $user->last_login_at = date('Y-m-d H:i:s');
        $user->save();
        //        if ($token = $this->getToken($user->id)) {
        //            return $token;
        //        }
        $token = $jwtService->create($user->id, $user->password, User::TOKEN_EXPIRE);
        $this->cacheToken($user->id, $token);
        return $token;
    }

    /**
     * 用户注册
     * @param $data
     * @return bool
     */
    public function register($data)
    {
        $data     = Arr::only($data, ['username', 'password', 'nickname', 'signature', 'avatar', 'sex']);
        $model    = new User($data);
        $validate = validator($data, [
            'username' => 'required|unique:' . $model->getTable(),
            'password' => ['required', ['regex', '/^[\w\-.?]{6,12}$/is']],
        ]);
        if ($validate->fails()) {
            throw  new \Exception($validate->errors()->first());
        }
        return $model->save();
    }

    /**
     * 缓存token
     * @param $uid
     * @param $token
     * @return bool
     */
    public function cacheToken($uid, $token)
    {
        return Cache::add(User::TOKEN_CACHE_KEY . $uid, $token, User::TOKEN_EXPIRE);
    }

    public function getToken($uid)
    {
        return Cache::get(User::TOKEN_CACHE_KEY . $uid);
    }
}
