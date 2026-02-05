<?php
namespace App\Core;

class View
{
    private $basePath;
    private $data = [];
    
    public function __construct()
    {
        $this->basePath = rtrim(VIEW_PATH, '/\\') . '/';
    }
    
    public function render($template, $data = [], $layout = 'main')
    {
        $this->data = array_merge($this->data, $data);
        extract($this->data);
        
        // Check if template exists
        $templateFile = $this->basePath . str_replace('.', '/', ltrim($template, '/')) . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \Exception("View template not found: {$template}");
        }
        
        // Start output buffering
        ob_start();
        include $templateFile;
        $content = ob_get_clean();
        
        // Wrap in layout if specified
        if ($layout) {
            $layoutFile = $this->basePath . 'layouts/' . $layout . '.php';
            
            if (!file_exists($layoutFile)) {
                throw new \Exception("Layout file not found: {$layout}");
            }
            
            ob_start();
            include $layoutFile;
            $content = ob_get_clean();
        }
        
        return $content;
    }
    
    public function partial($name, $data = [])
    {
        $partialFile = $this->basePath . 'partials/' . ltrim($name, '/') . '.php';
        
        if (!file_exists($partialFile)) {
            throw new \Exception("Partial file not found: {$name}");
        }
        
        extract($data);
        ob_start();
        include $partialFile;
        return ob_get_clean();
    }
    
    public function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    public function e($value)
    {
        return $this->escape($value);
    }
}