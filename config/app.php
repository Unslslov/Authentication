<?php
return [
    'name' => getenv('APP_NAME') ?? 'My App',
    'env' => getenv('APP_ENV') ?? 'dev',
    'debug' => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ?? false,
    'url' => getenv('APP_URL') ?? '',
    'timezone' => getenv('APP_TIMEZONE') ?? 'UTC',
    'key' => getenv('APP_KEY') ?? '',
];
?>