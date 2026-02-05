<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Auth;
use App\Core\Response;

class AuthMiddleware
{
    private $auth;
    private $response;
    
    public function __construct(Auth $auth, Response $response)
    {
        $this->auth = $auth;
        $this->response = $response;
    }
    
    public function handle(Request $request, $next, $role = null)
    {
        // Check if user is authenticated
        if (!$this->auth->check()) {
            // Store intended URL for redirect after login
            $_SESSION['intended_url'] = $request->getPath();
            
            // Flash message
            $_SESSION['flash']['error'] = 'Please login to access this page';
            
            // Redirect to login
            return $this->response->redirect('/login');
        }
        
        // Check role if specified
        if ($role && !$this->checkRole($role)) {
            $_SESSION['flash']['error'] = 'You do not have permission to access this page';
            return $this->response->redirect('/');
        }
        
        // Continue to next middleware/controller
        return $next($request);
    }
    
    private function checkRole($role)
    {
        $user = $this->auth->user();
        
        if ($role === 'admin') {
            return isset($user['role']) && $user['role'] === 'admin';
        }
        
        // Add more role checks as needed
        return true;
    }
}