<?php
namespace App\Core;

class Response
{
    private $headers = [];
    private $statusCode = 200;
    private $content;
    
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        http_response_code($code);
        return $this;
    }
    
    public function header($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    public function json($data, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->header('Content-Type', 'application/json');
        $this->content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $this;
    }
    
    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode);
        $this->header('Location', $url);
        $this->send();
        exit;
    }
    
    public function view($template, $data = [], $layout = 'main')
    {
        $view = new View();
        $this->content = $view->render($template, $data, $layout);
        $this->header('Content-Type', 'text/html');
        return $this;
    }
    
    public function send()
    {
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        if ($this->content !== null) {
            echo $this->content;
        }
        
        return $this;
    }
    
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $_SESSION['flash'][$k] = $v;
            }
        } else {
            $_SESSION['flash'][$key] = $value;
        }
        return $this;
    }
    
    public function withErrors($errors)
    {
        return $this->with('errors', $errors);
    }
}