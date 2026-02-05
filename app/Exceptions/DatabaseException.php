<?php
namespace App\Exceptions;

class DatabaseException extends \Exception
{
    protected $query;
    protected $params;
    
    public function __construct($message = "", $code = 0, $previous = null, $query = null, $params = [])
    {
        parent::__construct($message, $code, $previous);
        $this->query = $query;
        $this->params = $params;
    }
    
    public function getQuery()
    {
        return $this->query;
    }
    
    public function getParams()
    {
        return $this->params;
    }
    
    public function __toString()
    {
        $str = parent::__toString();
        
        if ($this->query) {
            $str .= "\nQuery: " . $this->query;
        }
        
        if (!empty($this->params)) {
            $str .= "\nParams: " . print_r($this->params, true);
        }
        
        return $str;
    }
}