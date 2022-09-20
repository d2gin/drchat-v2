<?php

namespace App\Api\Controllers;

use App\Api\Http\ApiResponse;
use App\Api\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function info(Request $request)
    {
        return ApiResponse::success($request->userinfo());
    }

    public function edit(Request $request)
    {
        $avatar    = $request->post('avatar');
        $nickname  = $request->post('nickname');
        $signature = $request->post('signature');
        $sex       = $request->post('sex');
        if(!in_array($sex,[0,1])) {
            $sex = null;
        }
        $data      = array_filter(compact('avatar', 'nickname', 'signature', 'sex'), function ($v) {
            return $v !== null;
        });
        $userId = $request->userId();
        User::query()->where('id', $userId)->update($data);
        return ApiResponse::success('修改成功');
    }
}
