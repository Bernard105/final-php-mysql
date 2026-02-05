<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Request;
use App\Core\Auth;
use App\Core\Container;

abstract class Controller
{
    protected $view;
    protected $session;
    protected $auth;
    protected $request;
    protected $db;
    
    public function __construct(Container $container)
    {
        $this->view = new View();
        $this->session = $container->get('session');
        $this->auth = new Auth($this->session);
        $this->request = new Request();
        $this->db = $container->get('db');
    }
    
    protected function render($template, $data = [], $layout = 'main')
    {
        return $this->view->render($template, $data, $layout);
    }
    
    protected function redirect($path)
    {
        header("Location: " . SITE_URL . $path);
        exit;
    }
    
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function validate(array $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = $this->request->input($field);
            
            foreach ($rulesArray as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[$field][] = "The {$field} field is required.";
                }
                
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) str_replace('min:', '', $rule);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters.";
                    }
                }
                
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) str_replace('max:', '', $rule);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "The {$field} may not be greater than {$max} characters.";
                    }
                }
                
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "The {$field} must be a valid email address.";
                }
                
                if (strpos($rule, 'unique:') === 0) {
                    [$table, $column] = explode(',', str_replace('unique:', '', $rule));
                    $exists = $this->db->fetch("SELECT * FROM {$table} WHERE {$column} = ?", [$value]);
                    if ($exists) {
                        $errors[$field][] = "The {$field} has already been taken.";
                    }
                }
            }
        }
        
        if (!empty($errors)) {
            $this->session->flash('errors', $errors);
            $this->session->flash('old', $this->request->all());
            return false;
        }
        
        return true;
    }
}