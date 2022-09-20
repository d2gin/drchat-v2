<?php

namespace App\Api\Controllers;

use App\Api\Http\ApiResponse;
use App\Api\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;

class PassportController extends Controller
{
    /**
     * 登录
     * @return ApiResponse
     */
    public function login()
    {
        $username = Request::post('username');
        $password = Request::post('password');
        $service  = new UserService();
        $token    = $service->login($username, $password);
        if (!$token) {
            return ApiResponse::error('登录失败');
        }
        return ApiResponse::success(['token' => $token,], '登录成功');
    }

    /**
     * 注册
     * @return ApiResponse
     */
    public function register()
    {
        $service = new UserService();
        $result  = $service->register(Request::post());
        if ($result) {
            return ApiResponse::success('注册成功');
        }
        return ApiResponse::error('注册失败');
    }
}
