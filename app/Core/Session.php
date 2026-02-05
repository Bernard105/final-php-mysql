<?php
namespace App\Core;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }
    
    
    public function getId()
    {
        return session_id();
    }

public function remove($key)
    {
        unset($_SESSION[$key]);
    }
    
    public function flash($key, $value = null)
    {
        // Store flash messages in a single bucket for compatibility with views/middleware
        if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }

        if ($value === null) {
            $val = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $val;
        }

        $_SESSION['flash'][$key] = $value;
    }
    
    public function destroy()
    {
        session_destroy();
    }
    
    public function regenerate()
    {
        session_regenerate_id(true);
    }
}