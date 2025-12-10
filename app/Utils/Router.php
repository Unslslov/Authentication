<?php

namespace App\Utils;

use Exception;

class Router
{
    private static array $routes = [];
    private static array $middleware = [];
    private static array $groupAttributes = [];
    private static $fallback;

    /**
     * Добавление маршрута
     */
    private static function addRoute(string $method, string $uri, $action, array $options = []): void
    {
        // Если есть групповые атрибуты, применяем их
        if (!empty(self::$groupAttributes)) {
            $uri = rtrim(self::$groupAttributes['prefix'] ?? '', '/') . '/' . ltrim($uri, '/');

            if (isset(self::$groupAttributes['middleware'])) {
                $options['middleware'] = array_merge(
                    (array)self::$groupAttributes['middleware'],
                    $options['middleware'] ?? []
                );
            }
        }

        // Нормализация URI
        $uri = '/' . trim($uri, '/');
        $uri = preg_replace('/\/+/', '/', $uri);

        // Подготовка действия
        if (is_string($action) && strpos($action, '@') !== false) {
            [$controller, $method] = explode('@', $action);
            $action = ['controller' => $controller, 'method' => $method];
        } elseif (is_array($action) && count($action) === 2) {
            $action = ['controller' => $action[0], 'method' => $action[1]];
        }

        // Сохраняем маршрут
        self::$routes[$method][$uri] = [
            'action' => $action,
            'middleware' => $options['middleware'] ?? [],
            'name' => $options['name'] ?? null,
        ];
    }

    /**
     * GET маршрут
     */
    public static function get(string $uri, $action, array $options = []): void
    {
        self::addRoute('GET', $uri, $action, $options);
    }

    /**
     * POST маршрут
     */
    public static function post(string $uri, $action, array $options = []): void
    {
        self::addRoute('POST', $uri, $action, $options);
    }

    /**
     * PUT маршрут
     */
    public static function put(string $uri, $action, array $options = []): void
    {
        self::addRoute('PUT', $uri, $action, $options);
    }

    /**
     * PATCH маршрут
     */
    public static function patch(string $uri, $action, array $options = []): void
    {
        self::addRoute('PATCH', $uri, $action, $options);
    }

    /**
     * DELETE маршрут
     */
    public static function delete(string $uri, $action, array $options = []): void
    {
        self::addRoute('DELETE', $uri, $action, $options);
    }

    /**
     * Любой метод
     */
    public static function any(string $uri, $action, array $options = []): void
    {
        self::addRoute('GET', $uri, $action, $options);
        self::addRoute('POST', $uri, $action, $options);
        self::addRoute('PUT', $uri, $action, $options);
        self::addRoute('PATCH', $uri, $action, $options);
        self::addRoute('DELETE', $uri, $action, $options);
    }

    /**
     * Группа маршрутов
     */
    public static function group(array $attributes, callable $callback): void
    {
        $previousGroupAttributes = self::$groupAttributes;

        // Объединяем атрибуты группы
        self::$groupAttributes = array_merge($previousGroupAttributes, $attributes);

        // Вызываем callback для добавления маршрутов внутри группы
        $callback();

        // Восстанавливаем предыдущие атрибуты
        self::$groupAttributes = $previousGroupAttributes;
    }

    /**
     * Регистрация middleware
     */
    public static function middleware(string $name, callable $callback): void
    {
        self::$middleware[$name] = $callback;
    }

    /**
     * Запуск маршрутизатора
     */
    public static function dispatch(): void
    {
        // Получаем текущий URI и метод
        $uri = self::getCurrentUri();
        $method = $_SERVER['REQUEST_METHOD'];

        // Проверяем наличие маршрута
        if (isset(self::$routes[$method][$uri])) {
            $route = self::$routes[$method][$uri];

            self::executeRoute($route);
            return;
        };

        // Проверяем параметрические маршруты
        foreach (self::$routes[$method] ?? [] as $routeUri => $route) {
            if (self::matchParametricRoute($routeUri, $uri, $params)) {
                self::executeRoute($route, $params);
                return;
            }
        }

        // 404 если нет маршрута
        self::handleNotFound();
    }

    /**
     * Выполнение маршрута
     */
    private static function executeRoute(array $route, array $params = []): void
    {
        try {
            // Выполняем middleware
            foreach ($route['middleware'] as $middlewareName) {
                if (!isset(self::$middleware[$middlewareName])) {
                    throw new Exception("Middleware '$middlewareName' not found");
                }

                $middlewareResult = call_user_func(self::$middleware[$middlewareName]);

                if ($middlewareResult === false) {
                    // Middleware прервал выполнение
                    return;
                }
            }

            // Выполняем действие
            $action = $route['action'];

            if (is_callable($action)) {
                // Closure
                call_user_func_array($action, $params);
            } elseif (is_array($action)) {
                // Controller@method
                $controllerName = $action['controller'];

                if (!class_exists($controllerName)) {
                    throw new Exception("Controller $controllerName not found");
                }

                $controller = new $controllerName();
                $methodName = $action['method'];

                if (!method_exists($controller, $methodName)) {
                    throw new Exception("Method $methodName not found in $controllerName");
                }

                // Устанавливаем параметры маршрута в контроллер
                if (method_exists($controller, 'setRouteParams')) {
                    $controller->setRouteParams($params);
                }

                // Вызываем метод контроллера
                call_user_func_array([$controller, $methodName], array_values($params));
            } else {
                throw new Exception("Invalid route action");
            }

        } catch (Exception $e) {
            echo $e;
            self::handleError($e);
        }
    }

    /**
     * Проверка параметрического маршрута
     */
    private static function matchParametricRoute(string $routeUri, string $requestUri, &$params): bool
    {
        // Преобразуем параметры {param} в регулярное выражение
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $routeUri);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        if (preg_match($pattern, $requestUri, $matches)) {
            // Получаем имена параметров
            preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $routeUri, $paramNames);

            $params = [];
            for ($i = 0; $i < count($paramNames[1]); $i++) {
                $params[$paramNames[1][$i]] = $matches[$i + 1];
            }

            return true;
        }

        return false;
    }

    /**
     * Получение текущего URI
     */
    private static function getCurrentUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Удаляем query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Нормализация
        $uri = '/' . trim($uri, '/');
        $uri = preg_replace('/\/+/', '/', $uri);

        return $uri;
    }

    /**
     * Обработка 404
     */
    private static function handleNotFound(): void
    {
        http_response_code(404);

        if (self::$fallback) {
            call_user_func(self::$fallback);
        } else {
            echo '<h1>404 - Страница не найдена</h1>';
            echo '<p>Запрашиваемая страница ' . htmlspecialchars(self::getCurrentUri()) . ' не существует.</p>';
        }
    }

    /**
     * Обработка ошибок
     */
    private static function handleError(Exception $e): void
    {
        http_response_code(500);

        if (defined('APP_DEBUG') && APP_DEBUG) {
            echo '<h1>Ошибка приложения</h1>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } else {
            echo '<h1>Внутренняя ошибка сервера</h1>';
            echo '<p>Пожалуйста, попробуйте позже.</p>';
        }
    }

    /**
     * Установка обработчика 404
     */
    public static function fallback(callable $handler): void
    {
        self::$fallback = $handler;
    }

    /**
     * Генерация URL по имени маршрута
     */
    public static function route(string $name, array $params = []): string
    {
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $uri => $route) {
                if ($route['name'] === $name) {
                    // Заменяем параметры в URI
                    foreach ($params as $key => $value) {
                        $uri = str_replace('{' . $key . '}', $value, $uri);
                    }
                    return $uri;
                }
            }
        }

        throw new Exception("Route with name '$name' not found");
    }

    /**
     * Получение всех маршрутов (для отладки)
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }
}
?>