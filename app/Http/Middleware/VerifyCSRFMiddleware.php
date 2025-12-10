<?php

namespace App\Http\Middleware;

class VerifyCSRFMiddleware
{
    /**
     * Проверка VerifyCSRFMiddleware токена
     */
    public static function handle(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                http_response_code(419);
                echo 'VerifyCSRFMiddleware token mismatch';
                exit;
            }
        }

        return true;
    }
}