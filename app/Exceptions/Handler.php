<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception) && !config('app.debug')) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //自定义异常类，输出错误信息给前端
        if ($exception instanceof CustomException) {
            return response()->json(['error' => $exception->getMessage()], 200);
        }
        //接口认证失败错误
        if ($exception instanceof ApiAuthException) {
            return response()->json([
                'errorMsg' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'result' => false,
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
        //捕获请求游戏后端接口出现的异常
        if ($exception instanceof GameServerException) {
            return response()->json([
                'result' => false,
                'code' => $exception->getCode(),
                'errorMsg' => $exception->getMessage(),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        //捕获api调用时的异常
        if ($exception instanceof ApiException) {
            return response()->json([
                'result' => false,
                'code' => $exception->getCode(),
                'errorMsg' => $exception->getMessage(),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        if ($exception instanceof ValidationException) {    //覆盖源码，表单验证错误消息写入errorMsg属性里面
            return response()->json([
                'result' => false,
                'code' => $exception->getCode(),
                'errorMsg' => $exception->validator->errors()->getMessages(),
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
