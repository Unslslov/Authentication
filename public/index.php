<?php

use App\Utils\Config;
use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;

// Определяем абсолютный путь к корню проекта
define('BASE_PATH', dirname(__DIR__));

// ====================
// ШАГ 1: Загрузка автолоадера
// ====================

require_once __DIR__ . '/../vendor/autoload.php';

// ====================
// ШАГ 2: Загрузка переменных окружения
// ====================

try {
    $dotenv = Dotenv::createImmutable(BASE_PATH);

    $dotenv->load();

    $dotenv->required(['APP_ENV']);

} catch (ValidationException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit(1);
} catch (\Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit(1);
}

Config::load(BASE_PATH);

// ====================
// ШАГ 3: Настройка обработки ошибок
// ====================

// Включаем вывод ошибок в зависимости от окружения
if (getenv('APP_DEBUG')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', BASE_PATH . '/storage/logs/php_errors.log');
}

// Регистрируем обработчик исключений
set_exception_handler(function (Throwable $exception) {
    http_response_code(500);

    if (getenv('APP_DEBUG')) {
        echo "<h1>Uncaught Exception</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . htmlspecialchars($exception->getLine()) . "</p>";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    } else {
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
    }

    // Логируем ошибку
    $logMessage = sprintf(
        "[%s] %s in %s on line %s\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );

    error_log($logMessage, 3, BASE_PATH . '/storage/logs/app.log');

    exit(1);
});

// ====================
// ШАГ 4: Создание приложения
// ====================

try {
    // Загружаем bootstrap файл приложения
    $app = require_once BASE_PATH . '/bootstrap/app.php';

    // Проверяем, что app - экземпляр Application
    if (!$app instanceof Application) {
        throw new RuntimeException('Application instance not returned from bootstrap/app.php');
    }
    // ====================
    // ШАГ 5: Обработка HTTP запроса
    // ====================

    // Генерация VerifyCSRFMiddleware токена если нет
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    require_once __DIR__ . '/../routes/web.php';

    \App\Utils\Router::dispatch();

} catch (Exception $e) {
    // Ловим все исключения на уровне приложения
    http_response_code(500);

    if (getenv('APP_DEBUG')) {
        echo "<h1>Application Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . htmlspecialchars($e->getLine()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>The application could not handle your request.</p>";
    }

    exit(1);
}