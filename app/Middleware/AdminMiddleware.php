<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Auth;
use App\Core\Response;

class AdminMiddleware
{
    private $auth;
    private $response;
    
    public function __construct(Auth $auth, Response $response)
    {
        $this->auth = $auth;
        $this->response = $response;
    }
    
    public function handle(Request $request, $next)
    {
        // Check if user is authenticated
        if (!$this->auth->check()) {
            $_SESSION['flash']['error'] = 'Please login to access admin area';
            return $this->response->redirect('/login');
        }
        
        // Check if user is admin
        if (!$this->auth->isAdmin()) {
            $_SESSION['flash']['error'] = 'You do not have permission to access admin area';
            return $this->response->redirect('/');
        }
        
        // Continue to next middleware/controller
        return $next($request);
    }
}