<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Services\EmailService;
use App\Core\Validator;

class UserController extends Controller
{
    private $userModel;
    private $orderModel;
    private $emailService;
    
    public function __construct($container)
    {
        parent::__construct($container);
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->emailService = new EmailService();
    }
    
    public function register()
    {
        if ($this->auth->check()) {
            return $this->redirect('/');
        }
        
        if ($this->request->isPost()) {
            $validator = new Validator($this->request->all());
            
            $rules = [
                'username' => 'required|min:3|max:50|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'address' => 'required|max:255',
                'mobile' => 'required|phone'
            ];
            
            if ($validator->validate($rules)) {
                $data = [
                    'username' => $this->request->post('username'),
                    'email' => $this->request->post('email'),
                    'password' => password_hash($this->request->post('password'), PASSWORD_DEFAULT),
                    'address' => $this->request->post('address'),
                    'mobile' => $this->request->post('mobile'),
                    'user_ip' => $_SERVER['REMOTE_ADDR'],
                    'user_image' => 'default.png',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $userId = $this->userModel->create($data);
                
                $this->auth->login($this->userModel->find($userId));
                $this->session->flash('success', 'Registration successful!');
                
                return $this->redirect('/');
            } else {
                $errors = $validator->errors();
                $this->session->flash('errors', $errors);
                $this->session->flash('old', $this->request->all());
            }
        }
        
        return $this->render('user/register', [
            'title' => 'Register - ' . SITE_NAME
        ]);
    }
    
    public function login()
    {
        if ($this->auth->check()) {
            return $this->redirect('/');
        }
        
        if ($this->request->isPost()) {
            $email = $this->request->post('email');
            $password = $this->request->post('password');
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Generate OTP for 2FA
                // In development, use a fixed OTP to make admin testing easy on local stacks (e.g., XAMPP).
                $otp = (defined('APP_ENV') && APP_ENV === 'development') ? '123456' : (string) rand(100000, 999999);
                $this->session->set('otp', $otp);
                $this->session->set('otp_expires', time() + 300); // 5 minutes
                $this->session->set('pending_user_id', $user['id']);
// Send OTP email (skip in development to avoid mail() issues on local environments)
                if (!(defined('APP_ENV') && APP_ENV === 'development')) {
                    try {
                        $this->emailService->sendOTP($user['email'], $user['username'], $otp);
                    } catch (\Exception $e) {
                        // Don't block login flow if mail fails; user can re-try login.
                    }
                }return $this->redirect('/verify-otp');
            }
            
            $this->session->flash('error', 'Invalid email or password');
        }
        
        return $this->render('user/login', [
            'title' => 'Login - ' . SITE_NAME
        ]);
    }
    
    public function verifyOtp()
    {
        $pendingUserId = $this->session->get('pending_user_id');
        
        if (!$pendingUserId) {
            return $this->redirect('/login');
        }
        
        if ($this->request->isPost()) {
            $userOtp = $this->request->post('otp');
            $storedOtp = $this->session->get('otp');
            $otpExpires = $this->session->get('otp_expires');
            
            if (time() > $otpExpires) {
                $this->session->flash('error', 'OTP has expired');
                $this->session->remove('pending_user_id');
                $this->session->remove('otp');
                $this->session->remove('otp_expires');
                return $this->redirect('/login');
            }
            
            if ($userOtp == $storedOtp) {
                $user = $this->userModel->find($pendingUserId);
                $this->auth->login($user);
                
                $this->session->remove('pending_user_id');
                $this->session->remove('otp');
                $this->session->remove('otp_expires');
                
                $this->session->flash('success', 'Login successful!');
                return $this->redirect('/');
            }
            
            $this->session->flash('error', 'Invalid OTP');
        }
        
        return $this->render('user/verify-otp', [
            'title' => 'Verify OTP - ' . SITE_NAME
        ]);
    }
    
    public function logout()
    {
        $this->auth->logout();
        $this->session->flash('success', 'You have been logged out');
        return $this->redirect('/');
    }


    public function profile()
    {
        if (!$this->auth->check()) return $this->redirect('/login');

        return $this->render('user/profile', [
            'user' => $_SESSION['user'] ?? null,
            'title' => 'Profile - ' . SITE_NAME
        ]);
    }

    public function updateProfile()
    {
        if (!$this->auth->check()) return $this->redirect('/login');

        if ($this->request->isPost()) {
            $user = $_SESSION['user'];
            $data = [
                'address' => $this->request->post('address') ?? $user['address'],
                'mobile' => $this->request->post('mobile') ?? $user['mobile'],
            ];
            $this->userModel->update($user['id'], $data);
            $_SESSION['user'] = $this->userModel->find($user['id']);
            $this->session->flash('success', 'Profile updated');
        }

        return $this->redirect('/profile');
    }

    public function orders()
    {
        if (!$this->auth->check()) return $this->redirect('/login');
        $userId = $_SESSION['user']['id'];
        $orders = $this->orderModel->getUserOrders($userId);
        return $this->render('user/orders', [
            'orders' => $orders,
            'title' => 'My Orders - ' . SITE_NAME
        ]);
    }

    public function viewOrder($id)
    {
        if (!$this->auth->check()) return $this->redirect('/login');
        $order = $this->orderModel->getOrderWithItems($id);
        if (!$order || (int)$order['user_id'] !== (int)($_SESSION['user']['id'] ?? 0)) {
            $this->session->flash('error', 'Order not found');
            return $this->redirect('/orders');
        }
        return $this->render('user/order-view', [
            'order' => $order,
            'title' => 'Order #' . $order['order_id'] . ' - ' . SITE_NAME
        ]);
    }

    // The following features are not implemented in this starter project.
    // Keep routes working without fatal errors.
    public function forgotPassword()
    {
        if ($this->request->isPost()) {
            $this->session->flash('info', 'Password reset flow is not configured in this demo.');
        }
        return $this->render('user/forgot-password', [
            'title' => 'Forgot Password - ' . SITE_NAME
        ]);
    }

    public function resetPassword($token)
    {
        if ($this->request->isPost()) {
            $this->session->flash('info', 'Password reset flow is not configured in this demo.');
            return $this->redirect('/login');
        }
        return $this->render('user/reset-password', [
            'token' => $token,
            'title' => 'Reset Password - ' . SITE_NAME
        ]);
    }

    public function wishlist() { return $this->redirect('/'); }
    public function addToWishlist($id) { return $this->redirect('/'); }
    public function removeFromWishlist($id) { return $this->redirect('/'); }
    public function addresses() { return $this->redirect('/profile'); }
    public function addAddress() { return $this->redirect('/profile'); }
    public function updateAddress($id) { return $this->redirect('/profile'); }
    public function deleteAddress($id) { return $this->redirect('/profile'); }
    public function changePassword() { return $this->redirect('/profile'); }
}
