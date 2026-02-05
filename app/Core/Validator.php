<?php
namespace App\Core;

class Validator
{
    private $data;
    private $errors = [];
    private $rules;
    
    public function __construct($data, $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }
    
    public function validate()
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = $this->parseRules($ruleString);
            $value = $this->getValue($field);
            
            foreach ($rules as $rule => $params) {
                $method = 'validate' . ucfirst($rule);
                
                if (method_exists($this, $method)) {
                    $this->$method($field, $value, $params);
                }
            }
        }
        
        return empty($this->errors);
    }
    
    private function parseRules($ruleString)
    {
        $rules = [];
        $parts = explode('|', $ruleString);
        
        foreach ($parts as $part) {
            if (strpos($part, ':') !== false) {
                [$rule, $params] = explode(':', $part, 2);
                $rules[$rule] = explode(',', $params);
            } else {
                $rules[$part] = [];
            }
        }
        
        return $rules;
    }
    
    private function getValue($field)
    {
        $keys = explode('.', $field);
        $value = $this->data;
        
        foreach ($keys as $key) {
            if (is_array($value) && isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }
        
        return $value;
    }
    
    // Validation methods
    private function validateRequired($field, $value, $params)
    {
        if (empty($value) && $value !== '0') {
            $this->errors[$field][] = "The {$field} field is required.";
        }
    }
    
    private function validateEmail($field, $value, $params)
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "The {$field} must be a valid email address.";
        }
    }
    
    private function validateMin($field, $value, $params)
    {
        $min = (int) ($params[0] ?? 0);
        if (strlen($value) < $min) {
            $this->errors[$field][] = "The {$field} must be at least {$min} characters.";
        }
    }
    
    private function validateMax($field, $value, $params)
    {
        $max = (int) ($params[0] ?? 0);
        if (strlen($value) > $max) {
            $this->errors[$field][] = "The {$field} may not be greater than {$max} characters.";
        }
    }
    
    private function validateNumeric($field, $value, $params)
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field][] = "The {$field} must be a number.";
        }
    }
    
    private function validateConfirmed($field, $value, $params)
    {
        $confirmationField = $field . '_confirmation';
        $confirmationValue = $this->getValue($confirmationField);
        
        if ($value !== $confirmationValue) {
            $this->errors[$field][] = "The {$field} confirmation does not match.";
        }
    }
    
    private function validateUnique($field, $value, $params)
    {
        // This would check database
        // Simplified for example
    }
    
    public function errors()
    {
        return $this->errors;
    }
    
    public function passes()
    {
        return empty($this->errors);
    }
    
    public function fails()
    {
        return !$this->passes();
    }
}