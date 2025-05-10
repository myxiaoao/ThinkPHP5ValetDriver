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
	// 检查是否是直接访问 PHP 文件（可能是随机生成的文件名）
        if (preg_match('#^/([^/]+\.php)#', $uri, $matches)) {
            $phpFile = $matches[1];

            // 检查public目录中是否存在该PHP文件
            if (file_exists($sitePath . '/public/' . $phpFile)) {
                $_SERVER['SCRIPT_NAME'] = '/' . $phpFile;
                $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/' . $phpFile;

                return $sitePath . '/public/' . $phpFile;
            }
        }

        // 处理 index.php 路径，检查是否有 s 参数
        if ($uri === '/index.php' && isset($_GET['s'])) {
            $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/public/index.php';
            $_SERVER['SERVER_NAME']     = $_SERVER['HTTP_HOST'];
            $_SERVER['SCRIPT_NAME']     = '/index.php';
            $_SERVER['PHP_SELF']        = '/index.php';
            // $_GET['s'] 已经存在，不需要再设置

            return $sitePath . '/public/index.php';
        }

        // 处理 index.php?s=xxx 格式的URL（如果URI中包含查询字符串）
        if (preg_match('#^/index\.php\?s=(.*)$#', $uri, $matches)) {
            $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/public/index.php';
            $_SERVER['SERVER_NAME']     = $_SERVER['HTTP_HOST'];
            $_SERVER['SCRIPT_NAME']     = '/index.php';
            $_SERVER['PHP_SELF']        = '/index.php';
            $_GET['s']                  = $matches[1];

            return $sitePath . '/public/index.php';
        }

        $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/public/index.php';
        $_SERVER['SERVER_NAME']     = $_SERVER['HTTP_HOST'];
        $_SERVER['SCRIPT_NAME']     = '/index.php';
        $_SERVER['PHP_SELF']        = '/index.php';
        $_GET['s']                  = $uri;

        return $sitePath . '/public/index.php';
    }
}
