<?php
namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Services\CartService;

class CartController extends Controller
{
    private $cartModel;
    private $productModel;
    private $cartService;
    
    public function __construct($container)
    {
        parent::__construct($container);
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        $this->cartService = new CartService($this->cartModel, $this->productModel, $this->session);
    }
    
    public function index()
    {
        $cartItems = $this->cartService->getItems();
        $total = $this->cartService->getTotal();
        
        return $this->render('cart/index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'title' => 'Shopping Cart - ' . SITE_NAME
        ]);
    }
    
    public function add($productId)
    {
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            $this->session->flash('error', 'Product not found');
            return $this->redirect('/');
        }
        
        $this->cartService->add($productId, 1);
        $_SESSION['cart_count'] = $this->cartService->getCartCount();
        $this->session->flash('success', 'Product added to cart');
        
        return $this->redirect('/cart');
    }
    
    public function update()
    {
        if ($this->request->isPost()) {
            $items = $this->request->post('quantity');
            
            foreach ($items as $productId => $quantity) {
                $this->cartService->update($productId, (int)$quantity);
            }
            
            $_SESSION['cart_count'] = $this->cartService->getCartCount();
            $this->session->flash('success', 'Cart updated');

            // If user clicked the "Checkout" button inside the cart form,
            // update quantities first, then go straight to checkout.
            $action = $this->request->post('action');
            if ($action === 'checkout') {
                return $this->redirect('/checkout');
            }
        }
        
        return $this->redirect('/cart');
    }
    
    public function remove($productId)
    {
        $this->cartService->remove($productId);
        $_SESSION['cart_count'] = $this->cartService->getCartCount();
        $this->session->flash('success', 'Item removed from cart');
        
        return $this->redirect('/cart');
    }
    
    public function clear()
    {
        $this->cartService->clear();
        $_SESSION['cart_count'] = 0;
        $this->session->flash('success', 'Cart cleared');
        return $this->redirect('/cart');
    }

    public function checkout()
    {
        if (!$this->auth->check()) {
            $this->session->flash('error', 'Please login to checkout');
            return $this->redirect('/login');
        }
        
        $cartItems = $this->cartService->getItems();
        
        if (empty($cartItems)) {
            $this->session->flash('error', 'Your cart is empty');
            return $this->redirect('/cart');
        }
        
        return $this->render('cart/checkout', [
            'cartItems' => $cartItems,
            'total' => $this->cartService->getTotal(),
            'title' => 'Checkout - ' . SITE_NAME
        ]);
    }
    
    public function processCheckout()
    {
        if (!$this->auth->check()) {
            $this->session->flash('error', 'Please login to checkout');
            return $this->redirect('/login');
        }

        $cartItems = $this->cartService->getItems();
        if (empty($cartItems)) {
            $this->session->flash('error', 'Your cart is empty');
            return $this->redirect('/cart');
        }

        $shippingAddress = $this->request->post('shipping_address') ?? ($_SESSION['user']['address'] ?? null);
        $paymentMethod = $this->request->post('payment_method') ?? 'cod';

        $items = [];
        $total = 0;
        foreach ($cartItems as $ci) {
            $items[] = [
                'product_id' => (int)$ci['product_id'],
                'quantity' => (int)$ci['quantity'],
                'price' => (float)$ci['product_price'],
            ];
            $total += ((float)$ci['product_price']) * ((int)$ci['quantity']);
        }

        $orderModel = new Order();
        $orderId = $orderModel->createOrder([
            'user_id' => (int)($_SESSION['user']['id'] ?? 0),
            'total_amount' => $total,
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
            'shipping_address' => $shippingAddress,
            'order_status' => 'pending',
            'items' => $items
        ]);

        if (!$orderId) {
            $this->session->flash('error', 'Failed to create order');
            return $this->redirect('/checkout');
        }

        $this->cartService->clear();
        $_SESSION['cart_count'] = 0;

        $this->session->set('last_order_id', $orderId);
        $this->session->flash('success', 'Order placed successfully!');
        return $this->redirect('/checkout/success');
    }

    public function checkoutSuccess()
    {
        $orderId = $this->session->get('last_order_id');
        return $this->render('cart/success', [
            'orderId' => $orderId,
            'title' => 'Order Success - ' . SITE_NAME
        ]);
    }

    public function checkoutCancel()
    {
        return $this->render('cart/cancel', [
            'title' => 'Checkout Cancelled - ' . SITE_NAME
        ]);
    }
}