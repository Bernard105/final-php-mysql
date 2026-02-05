<?php
namespace App\Exceptions;

class ValidationException extends \Exception
{
    protected $errors;
    protected $input;
    
    public function __construct($errors = [], $message = "Validation failed", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function setInput($input)
    {
        $this->input = $input;
        return $this;
    }
    
    public function getInput()
    {
        return $this->input;
    }
    
    public function __toString()
    {
        $str = parent::__toString();
        
        if (!empty($this->errors)) {
            $str .= "\nErrors:\n";
            foreach ($this->errors as $field => $errors) {
                foreach ($errors as $error) {
                    $str .= "  {$field}: {$error}\n";
                }
            }
        }
        
        return $str;
    }
}