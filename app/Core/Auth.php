<?php
namespace App\Core;

class Auth
{
    private $session;
    private $user = null;
    
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->user = $this->session->get('user');
    }
    
    public function login($user)
    {
        // Remove sensitive data
        unset($user['password']);
        
        $this->session->set('user', $user);
        $this->session->set('logged_in', true);
        $this->user = $user;
        
        return true;
    }
    
    public function logout()
    {
        $this->session->remove('user');
        $this->session->remove('logged_in');
        $this->user = null;
        
        return true;
    }
    
    public function check()
    {
        return $this->session->get('logged_in', false) && !empty($this->user);
    }
    
    public function user()
    {
        return $this->user;
    }
    
    public function id()
    {
        return $this->user['id'] ?? null;
    }
    
    public function isAdmin()
    {
        return isset($this->user['role']) && $this->user['role'] === 'admin';
    }
    
    public function guest()
    {
        return !$this->check();
    }
    
    public function attempt($credentials)
    {
        // This would typically check against database
        // Simplified for example
        $username = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;
        
        // You would normally query the database here
        return false;
    }
}