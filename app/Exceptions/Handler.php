<?php

namespace App\Exceptions;

use App\Api\Http\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function shouldReturnJson($request, Throwable $e)
    {
        return true;
    }

    protected function prepareJsonResponse($request, Throwable $e)
    {
        $message = $e->getMessage();
        if ($e instanceof NotFoundHttpException && !$message) {
            $message = 'Not found.';
        }
        $data = Arr::except($this->convertExceptionToArray($e), ['message']);
        return ApiResponse::error($message, $data ?: null);
    }
}
