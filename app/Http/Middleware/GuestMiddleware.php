<?php
namespace App\Http\Middleware;

class GuestMiddleware {
    public static function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            // Редирект на главную для авторизованных
            header('Location: /profile');
            exit;
        }

        return true;
    }
}
?>