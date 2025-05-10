<?php

namespace Valet\Drivers\Custom;

use Valet\Drivers\ValetDriver;

class ThinkPHP5ValetDriver extends ValetDriver
{
    /**
     * Determine if the driver serves the request.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return bool
     */
    public function serves(string $sitePath, string $siteName, string $uri): bool
    {
        return file_exists($sitePath . '/thinkphp/base.php') && file_exists($sitePath . '/think');
    }
	
    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string|false
     */
    public function isStaticFile(string $sitePath, string $siteName, string $uri)/*: string|false */
    {
        if (file_exists($staticFilePath = $sitePath . '/public' . $uri)
            && is_file($staticFilePath)) {
            return $staticFilePath;
        }

        return false;
    }
	
    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath(string $sitePath, string $siteName, string $uri): string
    {
        $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/public/index.php';
        $_SERVER['SERVER_NAME']     = $_SERVER['HTTP_HOST'];
        $_SERVER['SCRIPT_NAME']     = '/index.php';
        $_SERVER['PHP_SELF']        = '/index.php';
        $_GET['s']                  = $uri;

        return $sitePath . '/public/index.php';
    }
}
