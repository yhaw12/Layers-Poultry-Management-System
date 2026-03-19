<!-- <?php

// namespace App\Exceptions;

// use Throwable;
// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

// class Handler extends ExceptionHandler
// {
//     protected $dontReport = [];

//     protected $dontFlash = [
//         'current_password',
//         'password',
//         'password_confirmation',
//     ];

    // public function register(): void
    // {
    //     //
    // }

    // ✅ ADD IT HERE (inside the class)
    // public function render($request, Throwable $e)
    // {
    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }

    //     return parent::render($request, $e);
    // }
// } 