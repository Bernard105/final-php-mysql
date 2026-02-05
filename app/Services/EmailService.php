<?php
namespace App\Services;

class EmailService
{
    private $config;
    
    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
    }
    
    public function send($to, $subject, $template, $data = [])
    {
        // Extract data
        extract($data);
        
        // Build email content
        $body = $this->buildTemplate($template, $data);
        
        // Headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . $this->config['from']['address'] . ' <' . $this->config['from']['name'] . '>',
            'Reply-To: ' . $this->config['from']['address'],
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // Send email
        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
    
    private function buildTemplate($template, $data)
    {
        $templateFile = __DIR__ . '/../../views/emails/' . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \Exception("Email template not found: {$template}");
        }
        
        extract($data);
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
    
    public function sendOTP($to, $username, $otp)
    {
        return $this->send(
            $to,
            'Your OTP Code - ' . SITE_NAME,
            'otp',
            [
                'username' => $username,
                'otp' => $otp,
                'site_name' => SITE_NAME,
                'site_url' => SITE_URL
            ]
        );
    }
    
    public function sendOrderConfirmation($to, $username, $order)
    {
        return $this->send(
            $to,
            'Order Confirmation #' . ($order['order_id'] ?? ''),
            'order-confirmation',
            [
                'username' => $username,
                'order' => $order,
                'site_name' => SITE_NAME,
                'site_url' => SITE_URL
            ]
        );
    }
    
    public function sendPasswordReset($to, $username, $resetLink)
    {
        return $this->send(
            $to,
            'Password Reset Request - ' . SITE_NAME,
            'password-reset',
            [
                'username' => $username,
                'reset_link' => $resetLink,
                'site_name' => SITE_NAME,
                'site_url' => SITE_URL
            ]
        );
    }
    
    public function sendWelcome($to, $username)
    {
        return $this->send(
            $to,
            'Welcome to ' . SITE_NAME,
            'welcome',
            [
                'username' => $username,
                'site_name' => SITE_NAME,
                'site_url' => SITE_URL
            ]
        );
    }
}