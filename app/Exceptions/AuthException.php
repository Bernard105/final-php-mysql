<?php
namespace App\Exceptions;

class AuthException extends \Exception
{
    protected $field;
    
    public function __construct($message = "Authentication failed", $code = 0, $previous = null, $field = null)
    {
        parent::__construct($message, $code, $previous);
        $this->field = $field;
    }
    
    public function getField()
    {
        return $this->field;
    }
    
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }
    
    public static function invalidCredentials($field = 'email')
    {
        return new static('Invalid credentials', 401, null, $field);
    }
    
    public static function unauthenticated()
    {
        return new static('Unauthenticated', 401);
    }
    
    public static function unauthorized()
    {
        return new static('Unauthorized', 403);
    }
    
    public static function userNotFound()
    {
        return new static('User not found', 404, null, 'email');
    }
    
    public static function accountLocked()
    {
        return new static('Account is locked. Please contact administrator.', 423);
    }
}