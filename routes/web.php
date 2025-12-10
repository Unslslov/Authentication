<?php

use App\Utils\Router;
use App\Http\Middleware\AuthenticateMiddleware;
use App\Http\Middleware\GuestMiddleware;
use App\Http\Middleware\VerifyCSRFMiddleware;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// Регистрация middleware
Router::middleware('auth', [AuthenticateMiddleware::class, 'handle']);
Router::middleware('guest', [GuestMiddleware::class, 'handle']);
Router::middleware('csrf', [VerifyCSRFMiddleware::class, 'handle']);

// Главная страница
Router::get('/', [HomeController::class, 'index']);

// Группа маршрутов для гостей
Router::group(['middleware' => 'guest'], function () {
    // Регистрация
    Router::get('/register', [AuthController::class, 'showRegisterForm']);
    Router::post('/register', [AuthController::class, 'register']);

    // Авторизация
    Router::get('/login', [AuthController::class, 'showLoginForm']);
    Router::post('/login', [AuthController::class, 'login']);
});

// Группа защищенных маршрутов
Router::group(['middleware' => 'auth'], function () {
    // Профиль
//    Router::group(['middleware' => 'csrf'], function () {
    Router::get('/profile', [ProfileController::class, 'show'], ['middleware' => ['auth', 'csrf']]);
//    });

    // Выход
    Router::post('/logout', [AuthController::class, 'logout']);
});

// Обработчик 404
//Router::fallback(function() {
//    http_response_code(404);
//    echo view('errors/404');
//});