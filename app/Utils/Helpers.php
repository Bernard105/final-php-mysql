<?php
namespace App\Utils;

class Helpers
{
    public static function dd($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
    
    public static function dump($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
    
    public static function esc($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public static function e($string)
    {
        return self::esc($string);
    }
    
    public static function asset($path)
    {
        return SITE_URL . '/assets/' . ltrim($path, '/');
    }
    
    public static function url($path = '')
    {
        return SITE_URL . '/' . ltrim($path, '/');
    }
    
    public static function route($name, $params = [])
    {
        // This would be more sophisticated in a real router
        $routes = [
            'home' => '/',
            'login' => '/login',
            'register' => '/register',
            'cart' => '/cart',
            'checkout' => '/checkout',
            'products' => '/products',
            'admin.dashboard' => '/admin',
            'admin.products' => '/admin/products'
        ];
        
        if (!isset($routes[$name])) {
            return '#';
        }
        
        $url = $routes[$name];
        
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url = str_replace('{' . $key . '}', $value, $url);
            }
        }
        
        return SITE_URL . $url;
    }
    
    public static function old($key, $default = '')
    {
        return $_SESSION['old'][$key] ?? $default;
    }
    
    public static function flash($key)
    {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    
    public static function hasFlash($key)
    {
        return isset($_SESSION['flash'][$key]);
    }
    
    public static function formatPrice($price, $currency = 'VND')
    {
        $formatted = number_format($price, 0, ',', '.');
        
        if ($currency === 'VND') {
            return $formatted . ' ₫';
        } elseif ($currency === 'USD') {
            return '$' . number_format($price, 2);
        }
        
        return $formatted;
    }
    
    public static function formatDate($date, $format = 'd/m/Y')
    {
        if (!$date) return '';
        
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date($format, $timestamp);
    }
    
    public static function limitWords($string, $limit = 100, $end = '...')
    {
        $words = preg_split('/\s+/', $string);
        
        if (count($words) <= $limit) {
            return $string;
        }
        
        return implode(' ', array_slice($words, 0, $limit)) . $end;
    }
    
    public static function slug($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9\s-]/', '', $string);
        $string = strtolower(trim($string));
        $string = preg_replace('/\s+/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return $string;
    }
    
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
    
    public static function csrfField()
    {
        $token = $_SESSION['csrf_token'] ?? self::generateToken();
        $_SESSION['csrf_token'] = $token;
        
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
    
    public static function csrfToken()
    {
        return $_SESSION['csrf_token'] ?? self::generateToken();
    }
    
    public static function verifyCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}