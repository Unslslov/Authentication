<?php

namespace App\Utils;

class BaseController
{
    /**
     * Параметры маршрута
     */
    protected array $routeParams = [];

    /**
     * Установка параметров маршрута
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    /**
     * Получение параметра маршрута
     */
    public function getRouteParam(string $key, $default = null)
    {
        return $this->routeParams[$key] ?? $default;
    }

    /**
     * Рендер представления
     */
    protected function view(string $view, array $data = []): void
    {
        // Извлекаем переменные из массива данных
        extract($data);

        // Путь к файлу представления
        $viewFile = BASE_PATH . "/resources/views/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$view}");
        }

        // Включаем файл представления
        require_once $viewFile;
    }

    /**
     * Редирект
     */
    protected function redirect(string $url, int $statusCode = 303): void
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * JSON ответ
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Проверка, является ли запрос AJAX
     */
    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Получение данных из запроса
     */
    protected function getRequestData(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $_GET;
        }

        // Для PUT, PATCH, DELETE и т.д.
        parse_str(file_get_contents('php://input'), $data);
        return $data;
    }

    /**
     * Проверка аутентификации пользователя
     */
    protected function checkAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    /**
     * Получение ID текущего пользователя
     */
    protected function getCurrentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Добавление флеш-сообщения
     */
    protected function addFlash(string $type, string $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][$type][] = $message;
    }

    /**
     * Проверка VerifyCSRFMiddleware токена (опционально, но рекомендуется)
     */
    protected function verifyCsrfToken(): bool
    {
        $token = $this->getRequestData()['csrf_token'] ?? '';
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Генерация VerifyCSRFMiddleware токена
     */
    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}