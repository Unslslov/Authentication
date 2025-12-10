<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
use App\Utils\Config;

define('BASE_PATH', '/var/www/html');
Config::clearCache(BASE_PATH);
echo "Config cache cleared successfully.\n";
