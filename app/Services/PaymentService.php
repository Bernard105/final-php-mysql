<?php
namespace App\Services;

class PaymentService
{
    private $config;
    
    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/payment.php';
    }
    
    public function process($amount, $method, $data = [])
    {
        switch ($method) {
            case 'cod':
                return $this->processCOD($amount, $data);
            case 'card':
                return $this->processCard($amount, $data);
            case 'paypal':
                return $this->processPayPal($amount, $data);
            case 'momo':
                return $this->processMoMo($amount, $data);
            case 'bank_transfer':
                return $this->processBankTransfer($amount, $data);
            default:
                throw new \Exception('Unsupported payment method');
        }
    }
    
    private function processCOD($amount, $data)
    {
        return [
            'success' => true,
            'method' => 'cod',
            'transaction_id' => null,
            'amount' => $amount,
            'status' => 'pending',
            'message' => 'Payment will be collected on delivery'
        ];
    }
    
    private function processCard($amount, $data)
    {
        // This is a mock implementation
        // In production, integrate with Stripe, PayPal, etc.
        
        $cardNumber = $data['card_number'] ?? '';
        $expiry = $data['expiry'] ?? '';
        $cvc = $data['cvc'] ?? '';
        $name = $data['name'] ?? '';
        
        // Validate card details (mock validation)
        if (empty($cardNumber) || empty($expiry) || empty($cvc) || empty($name)) {
            throw new \Exception('Invalid card details');
        }
        
        // Simulate processing
        $transactionId = 'CARD_' . strtoupper(uniqid());
        
        return [
            'success' => true,
            'method' => 'card',
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'status' => 'completed',
            'message' => 'Card payment processed successfully'
        ];
    }
    
    private function processPayPal($amount, $data)
    {
        // Mock PayPal integration
        $transactionId = 'PAYPAL_' . strtoupper(uniqid());
        
        return [
            'success' => true,
            'method' => 'paypal',
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'status' => 'completed',
            'message' => 'PayPal payment processed successfully'
        ];
    }
    
    private function processMoMo($amount, $data)
    {
        // Mock MoMo integration
        $phone = $data['phone'] ?? '';
        
        if (empty($phone)) {
            throw new \Exception('Phone number required for MoMo');
        }
        
        $transactionId = 'MOMO_' . strtoupper(uniqid());
        
        return [
            'success' => true,
            'method' => 'momo',
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'status' => 'pending',
            'message' => 'MoMo payment initiated. Please confirm on your phone.',
            'instructions' => [
                'phone' => $this->config['momo']['phone'] ?? '0900 123 456',
                'name' => $this->config['momo']['name'] ?? 'E-COMMERCE STORE',
                'content' => 'PAY_' . $transactionId
            ]
        ];
    }
    
    private function processBankTransfer($amount, $data)
    {
        $transactionId = 'BANK_' . strtoupper(uniqid());
        
        return [
            'success' => true,
            'method' => 'bank_transfer',
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'status' => 'pending',
            'message' => 'Please transfer to our bank account',
            'instructions' => [
                'bank' => $this->config['bank']['name'] ?? 'Vietcombank',
                'account_number' => $this->config['bank']['account'] ?? '0123 456 789',
                'account_name' => $this->config['bank']['holder'] ?? 'E-COMMERCE STORE',
                'content' => 'PAY_' . $transactionId
            ]
        ];
    }
    
    public function verifyPayment($transactionId, $method)
    {
        // Mock verification
        // In production, verify with payment gateway
        
        return [
            'verified' => true,
            'transaction_id' => $transactionId,
            'status' => 'completed',
            'verified_at' => date('Y-m-d H:i:s')
        ];
    }
    
    public function getPaymentMethods()
    {
        return [
            'cod' => [
                'name' => 'Cash on Delivery',
                'description' => 'Pay when you receive the goods',
                'icon' => 'cash',
                'fee' => 0
            ],
            'card' => [
                'name' => 'Credit/Debit Card',
                'description' => 'Pay with Visa, Mastercard',
                'icon' => 'credit-card',
                'fee' => 0
            ],
            'paypal' => [
                'name' => 'PayPal',
                'description' => 'Pay with your PayPal account',
                'icon' => 'paypal',
                'fee' => 0
            ],
            'momo' => [
                'name' => 'MoMo',
                'description' => 'Mobile payment via MoMo',
                'icon' => 'mobile-alt',
                'fee' => 0
            ],
            'bank_transfer' => [
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer',
                'icon' => 'university',
                'fee' => 0
            ]
        ];
    }
}