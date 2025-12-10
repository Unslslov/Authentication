<?php

/**
 * Bootstrap the application
 */

// Создаем необходимые директории
$directories = [
    BASE_PATH . '/storage',
    BASE_PATH . '/storage/logs',
    BASE_PATH . '/storage/framework',
    BASE_PATH . '/storage/framework/cache',
    BASE_PATH . '/storage/framework/sessions',
    BASE_PATH . '/storage/framework/views',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
//        echo "Created directory: " . str_replace(BASE_PATH . '/', '', $directory) . "\n";
    }
}

// Временный класс Application для тестирования
class Application
{
    public function handle(string $uri, string $method)
    {
        // Временная обработка запроса
        return [
            'status' => 'success',
            'message' => 'Application is running!',
            'data' => [
                'app_name' => App\Utils\Env::get('APP_NAME', 'App'),
                'environment' => App\Utils\Env::get('APP_ENV', 'production'),
                'debug' => App\Utils\Env::get('APP_DEBUG', false),
                'database' => [
                    'host' => App\Utils\Env::get('DB_HOST'),
                    'database' => App\Utils\Env::get('DB_DATABASE'),
                ],
                'request' => [
                    'uri' => $uri,
                    'method' => $method,
                    'time' => date('Y-m-d H:i:s')
                ]
            ]
        ];
    }
}

// Создаем и возвращаем экземпляр приложения
return new Application();