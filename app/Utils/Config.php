<?php
namespace App\Utils;

class Config {
    private static $config;
    private static bool $isCached = false;
    private static $cacheFile = 'config_cache.php';

    public static function load(string $path)
    {
        $cachePath = $path . '/storage/cache/' . self::$cacheFile;

        if(file_exists($cachePath)) {
            self::$config = require $cachePath;
            self::$isCached = true;
            return;
        }

        $configFiles = glob($path . '/config/*.php');
        foreach ($configFiles as $file)
        {
            $key = basename($file, '.php');
            self::$config[$key] = require $file;
        }

        self::cache($path);
    }

    public static function cache(string $path) : void
    {
        if(self::$isCached) {
            return;
        }

        $cachePath = $path . '/storage/cache/';

        if(!file_exists($cachePath)) {
            mkdir($cachePath, 0775, true);
        }

        $cacheFiles = $cachePath . self::$cacheFile;
        $config = var_export(self::$config, true);

        file_put_contents($cacheFiles,"<?php\nreturn $config;\n");
    }

    public static function clearCache(string $path) : void
    {
        $cachePath = $path . '/storage/cache/' . self::$cacheFile;

        echo $cachePath;
        if(file_exists($cachePath)) {
            unlink($cachePath);
        }
    }

    public static function get(string $key, $default = null)
    {
        $parts = explode('.', $key);
        $value = self::$config;

        foreach ($parts as $part)
        {
            if (!isset($value[$part])){
                return $default;
            }

            $value = $value[$part];
        }
        return $value;
    }
}
?>