<?php
namespace App\Core;

class Request
{
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function getPath()
    {
        // Use only the path part of the URI (no query string)
        $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        // If the project is hosted in a subdirectory, strip the base path so
        // routes can be defined as "/", "/products", ...
        //
        // Note: When using a root-level .htaccess that rewrites into /public,
        // Apache keeps REQUEST_URI as the *original* URL (without /public),
        // while SCRIPT_NAME points to /public/index.php. We therefore strip
        // both the script directory and (if applicable) its parent.
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = rtrim($scriptDir, '/');

        if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($uriPath, $scriptDir)) {
            $uriPath = substr($uriPath, strlen($scriptDir));
            if ($uriPath === '') {
                $uriPath = '/';
            }
        }

        // Also strip the parent directory when entry script is under /public
        // but the visible URL is not.
        if ($scriptDir !== '' && preg_match('#/public$#', $scriptDir)) {
            $baseDir = preg_replace('#/public$#', '', $scriptDir);
            $baseDir = rtrim($baseDir, '/');
            if ($baseDir !== '' && $baseDir !== '/' && str_starts_with($uriPath, $baseDir)) {
                $uriPath = substr($uriPath, strlen($baseDir));
                if ($uriPath === '') {
                    $uriPath = '/';
                }
            }
        }

        return $uriPath;
    }
    
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    public function file($key = null)
    {
        if ($key === null) {
            return $_FILES;
        }
        return $_FILES[$key] ?? null;
    }
    
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }
    
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }
    
    public function input($key, $default = null)
    {
        if ($this->isPost()) {
            return $this->post($key, $default);
        }
        return $this->get($key, $default);
    }
    
    public function all()
    {
        return array_merge($this->get(), $this->post());
    }
}