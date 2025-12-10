<?php
namespace App\Http\Middleware;

class AuthenticateMiddleware {
    public static function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            // Сохраняем URL для редиректа после входа
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];

            // Редирект на страницу входа
            header('Location: /login');
            exit;
        }

        return true;
    }
}
?>