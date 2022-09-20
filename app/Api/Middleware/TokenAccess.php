<?php

namespace App\Api\Middleware;

use App\Api\Http\ApiResponse;
use App\Api\Models\User;
use App\Api\Services\Jwt;
use App\Api\Services\StatusCode;
use Closure;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TokenAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next, $authMode = 'auth')
    {
        $token      = $request->header('token');
        $jwtService = new Jwt();
        Request::macro('userinfo', function ($column = null) {
            return null;
        });
        Request::macro('isLogin', function () use ($request) {
            return !empty($request->userinfo('id'));
        });
        Request::macro('userId', function () use ($request) {
            return $request->userinfo('id');
        });
        try {
            if (!$token) {
                throw new \Exception('token empty');
            }
            $tokenInfo    = $jwtService->decode($token);
            $uid          = $tokenInfo['uid'];
            $passwordHash = $tokenInfo['hash'];
            $user         = User::query()->where('id', $uid)->first();
            if (!$user) {
                return ApiResponse::code(StatusCode::STATUS_NEED_LOGIN)->message('非法用户');
            } else if ($passwordHash != $user->password) {// @todo 待优化
                return ApiResponse::code(StatusCode::STATUS_NEED_LOGIN)->message('请重新登录');
            }
            // @todo 用户禁用
            //else if(!$user->status) {}
            Request::macro('userinfo', function ($column = null) use ($user) {
                if ($column) return $user[$column] ?? '';
                $user = Arr::except($user->toArray(), ['password', 'deleted_at']);
                return $user;
            });
        } catch (ExpiredException $e) {
            if ($authMode == 'auth') return ApiResponse::code(StatusCode::STATUS_NEED_LOGIN)->message('登录已过期');
        } catch (\Throwable $e) {
            if ($authMode == 'auth') return ApiResponse::code(StatusCode::STATUS_NEED_LOGIN)->message('请先登录');
        }
        return $next($request);
    }
}
