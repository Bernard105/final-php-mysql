<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\NewsletterSubscriber;

class HomeController extends Controller
{
    private $productModel;
    private $categoryModel;
    private $brandModel;
    private $newsletterModel;
    
    public function __construct($container)
    {
        parent::__construct($container);
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
        $this->newsletterModel = new NewsletterSubscriber();
    }
    
    public function index()
    {
        $products = $this->productModel->getRandom(9);
        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();
        
        return $this->render('home/index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'title' => 'Home - ' . SITE_NAME
        ]);
    }
    
    public function products()
    {
        // Filters
        $filters = [
            'q' => $this->request->get('q'),
            'category_id' => $this->request->get('category_id'),
            'brand_id' => $this->request->get('brand_id'),
            'min_price' => $this->request->get('min_price'),
            'max_price' => $this->request->get('max_price'),
            'sort' => $this->request->get('sort') ?: 'newest',
        ];

        $page = (int)($this->request->get('page') ?? 1);
        $perPage = (int)($this->request->get('per_page') ?? 9);

        $result = $this->productModel->paginateFiltered($filters, $page, $perPage);
        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();

        return $this->render('products/index', [
            'products' => $result['items'],
            'pagination' => $result,
            'filters' => $filters,
            'categories' => $categories,
            'brands' => $brands,
            'title' => 'All Products - ' . SITE_NAME
        ]);
    }
    
    public function productDetail($id)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->session->flash('error', 'Product not found');
            return $this->redirect('/');
        }
        
        return $this->render('products/detail', [
            'product' => $product,
            'title' => $product['product_title'] . ' - ' . SITE_NAME
        ]);
    }
    
    public function search()
    {
        // Keep /search working, but support the same filters + pagination as /products
        $filters = [
            'q' => $this->request->get('q'),
            'category_id' => $this->request->get('category_id'),
            'brand_id' => $this->request->get('brand_id'),
            'min_price' => $this->request->get('min_price'),
            'max_price' => $this->request->get('max_price'),
            'sort' => $this->request->get('sort') ?: 'newest',
        ];

        $page = (int)($this->request->get('page') ?? 1);
        $perPage = (int)($this->request->get('per_page') ?? 9);

        $result = $this->productModel->paginateFiltered($filters, $page, $perPage);
        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();

        return $this->render('products/search', [
            'products' => $result['items'],
            'pagination' => $result,
            'filters' => $filters,
            'categories' => $categories,
            'brands' => $brands,
            'keyword' => $filters['q'],
            'title' => 'Search Results - ' . SITE_NAME
        ]);
    }


    public function category($id)
    {
        $products = $this->productModel->getByCategory($id);
        return $this->render('products/category', [
            'products' => $products,
            'categoryId' => $id,
            'title' => 'Category - ' . SITE_NAME
        ]);
    }

    public function brand($id)
    {
        $products = $this->productModel->getByBrand($id);
        return $this->render('products/brand', [
            'products' => $products,
            'brandId' => $id,
            'title' => 'Brand - ' . SITE_NAME
        ]);
    }

    /**
     * Newsletter subscribe: stores to DB and attempts to send mail.
     */
    public function subscribeNewsletter()
    {
        $email = trim((string)($this->request->post('email') ?? ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['newsletter_err'] = 'Please enter a valid email address.';
            return $this->redirect('/#newsletter');
        }

        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $res = $this->newsletterModel->subscribe($email, $ip);

            // Try sending a simple confirmation email (non-blocking)
            $subject = 'Newsletter subscription - ' . SITE_NAME;
            $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_ADDRESS . ">\r\n";
            $headers .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";

            $body = "Thanks for subscribing to " . SITE_NAME . "!\n\n";
            $body .= "You will receive weekly updates and deals.\n";
            $body .= "If you didn't request this, you can ignore this email.\n";

            // Some local environments don't have mail configured; ignore failures.
            @mail($email, $subject, $body, $headers);

            if (($res['status'] ?? '') === 'exists') {
                $_SESSION['newsletter_ok'] = 'You are already subscribed. See you in the next email!';
            } else {
                $_SESSION['newsletter_ok'] = 'Subscribed successfully! Please check your inbox.';
            }
        } catch (\Throwable $e) {
            $_SESSION['newsletter_err'] = 'Could not subscribe right now. Please try again.';
        }

        return $this->redirect('/#newsletter');
    }

}
