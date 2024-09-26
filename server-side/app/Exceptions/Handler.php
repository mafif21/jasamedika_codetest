<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => 404,
                    'message' => 'data not found'
                ], 404);
            }

            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 404,
                    'message' => 'data not found'
                ], 404);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 500,
                    'message' => $exception->getMessage()
                ], 500);
            }

            return parent::render($request, $exception);
        }

        return parent::render($request, $exception);
    }
}
