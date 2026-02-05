<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Core\Session;

class CartService
{
    private $cartModel;
    private $productModel;
    private $session;
    
    public function __construct(Cart $cartModel, Product $productModel, Session $session)
    {
        $this->cartModel = $cartModel;
        $this->productModel = $productModel;
        $this->session = $session;
    }
    
    public function addItem($productId, $quantity = 1)
    {
        // Check product exists and in stock
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            throw new \Exception('Product not found');
        }
        
        if (isset($product['stock_quantity']) && $product['stock_quantity'] < $quantity) {
            throw new \Exception('Insufficient stock');
        }
        
        // Get user ID or session ID
        $userId = $this->session->get('user')['id'] ?? null;
        $sessionId = $userId ? null : $this->session->getId();
        
        // Add to cart
        $cartId = $this->cartModel->addItem($userId, $productId, $quantity, $sessionId);
        
        return $cartId;
    }
    
    public function updateQuantity($cartId, $quantity)
    {
        return $this->cartModel->updateQuantity($cartId, $quantity);
    }
    
    public function removeItem($cartId)
    {
        return $this->cartModel->delete($cartId);
    }
    
    public function getCart()
    {
        $userId = $this->session->get('user')['id'] ?? null;
        $sessionId = $this->session->getId();
        
        if ($userId) {
            return $this->cartModel->getUserCart($userId);
        } else {
            return $this->cartModel->getCartBySession($sessionId);
        }
    }
    
    public function getCartCount()
    {
        $cartItems = $this->getCart();
        $count = 0;
        
        foreach ($cartItems as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }
    
    public function getCartTotal()
    {
        $cartItems = $this->getCart();
        $total = 0;
        
        foreach ($cartItems as $item) {
            $total += $item['product_price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    public function clearCart()
    {
        $userId = $this->session->get('user')['id'] ?? null;
        
        if ($userId) {
            return $this->cartModel->clearUserCart($userId);
        }
        
        return false;
    }
    
    public function migrateToUser($userId)
    {
        $sessionId = $this->session->getId();
        return $this->cartModel->migrateSessionCart($sessionId, $userId);
    }
    
    public function getCartSummary()
    {
        $cartItems = $this->getCart();
        
        $summary = [
            'items' => [],
            'subtotal' => 0,
            'shipping' => 0,
            'tax' => 0,
            'total' => 0,
            'item_count' => 0
        ];
        
        foreach ($cartItems as $item) {
            $itemTotal = $item['product_price'] * $item['quantity'];
            
            $summary['items'][] = [
                'id' => $item['product_id'],
                'title' => $item['product_title'],
                'price' => $item['product_price'],
                'quantity' => $item['quantity'],
                'total' => $itemTotal,
                'image' => $item['product_image1']
            ];
            
            $summary['subtotal'] += $itemTotal;
            $summary['item_count'] += $item['quantity'];
        }
        
        // Calculate shipping (example: free over $50, otherwise $5)
        $summary['shipping'] = $summary['subtotal'] > 50 ? 0 : 5;
        
        // Calculate tax (example: 10%)
        $summary['tax'] = $summary['subtotal'] * 0.1;
        
        // Calculate total
        $summary['total'] = $summary['subtotal'] + $summary['shipping'] + $summary['tax'];
        
        return $summary;
    }
    // Backward-compatible aliases used by controllers/views
    public function getItems()
    {
        return $this->getCart();
    }

    public function getTotal()
    {
        return $this->getCartTotal();
    }

    public function add($productId, $quantity = 1)
    {
        return $this->addItem($productId, $quantity);
    }

    public function update($productId, $quantity)
    {
        // update by product_id within current cart (user/session)
        $userId = $this->session->get('user')['id'] ?? null;
        $sessionId = $this->session->getId();
        $existing = $this->cartModel->findItem($userId, (int)$productId, $userId ? null : $sessionId);
        if (!$existing) return false;
        return $this->cartModel->updateQuantity($existing['cart_id'], (int)$quantity);
    }

    public function remove($productId)
    {
        $userId = $this->session->get('user')['id'] ?? null;
        $sessionId = $this->session->getId();
        $existing = $this->cartModel->findItem($userId, (int)$productId, $userId ? null : $sessionId);
        if (!$existing) return false;
        return $this->cartModel->delete($existing['cart_id']);
    }

    public function clear()
    {
        $userId = $this->session->get('user')['id'] ?? null;
        if ($userId) return $this->cartModel->clearUserCart($userId);
        return $this->cartModel->clearSessionCart($this->session->getId());
    }

}
