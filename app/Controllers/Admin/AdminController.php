<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    private $orderModel;
    private $productModel;
    private $userModel;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->userModel = new User();
    }

    public function dashboard()
    {
        // Base counts
        $totalProducts = (int)$this->productModel->count();
        $totalOrders = (int)$this->orderModel->count();
        $totalUsers = (int)$this->userModel->count();

        // Revenue + status breakdown (best-effort; depends on schema)
        $totalRevenueRow = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount),0) AS total_revenue FROM orders WHERE order_status IN ('delivered','shipped','processing','pending')"
        );
        $totalRevenue = (float)($totalRevenueRow['total_revenue'] ?? 0);

        $todayRevenueRow = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount),0) AS today_revenue FROM orders WHERE DATE(created_at)=CURDATE() AND order_status != 'cancelled'"
        );
        $todayRevenue = (float)($todayRevenueRow['today_revenue'] ?? 0);

        $statusCounts = $this->db->fetchAll(
            "SELECT order_status, COUNT(*) AS c FROM orders GROUP BY order_status"
        );
        $statusMap = [];
        foreach ($statusCounts as $r) {
            $statusMap[$r['order_status']] = (int)$r['c'];
        }

        // Recent activity
        $recentOrders = $this->orderModel->getRecentOrders(10);
        $recentUsers = $this->db->fetchAll(
            "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 8"
        );

        // Top products by quantity sold (order_items)
        $topProducts = $this->db->fetchAll(
            "SELECT oi.product_id, p.product_title, SUM(oi.quantity) AS qty_sold
             FROM order_items oi
             LEFT JOIN products p ON p.product_id = oi.product_id
             GROUP BY oi.product_id, p.product_title
             ORDER BY qty_sold DESC
             LIMIT 8"
        );

        return $this->render('admin.dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => [
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
                'total_users' => $totalUsers,
                'total_revenue' => $totalRevenue,
                'today_revenue' => $todayRevenue,
                'status_map' => $statusMap,
            ],
            'recent_orders' => $recentOrders,
            'recent_users' => $recentUsers,
            'top_products' => $topProducts,
        ], 'admin');
    }

    public function orders()
    {
        $status = $this->request->get('status');
        $search = $this->request->get('search');

        $orders = $this->orderModel->searchOrders($status, $search);

        return $this->render('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'title' => 'Manage Orders'
        ], 'admin');
    }

    public function viewOrder($id)
    {
        $order = $this->orderModel->getOrderWithItems((int)$id);

        if (!$order) {
            $this->session->flash('error', 'Order not found');
            return $this->redirect('/admin/orders');
        }

        return $this->render('admin.orders.view', [
            'order' => $order,
            'title' => 'Order #' . (int)($order['order_id'] ?? $id)
        ], 'admin');
    }

    public function updateOrderStatus($id)
    {
        if ($this->request->isPost()) {
            $status = $this->request->post('status');

            if ($this->orderModel->updateStatus((int)$id, $status)) {
                $this->session->flash('success', 'Order status updated successfully');
            } else {
                $this->session->flash('error', 'Failed to update order status');
            }
        }

        return $this->redirect('/admin/orders/' . (int)$id);
    }

    public function generateInvoice($id)
    {
        $order = $this->orderModel->getOrderWithItems((int)$id);
        if (!$order) {
            $this->session->flash('error', 'Order not found');
            return $this->redirect('/admin/orders');
        }

        return $this->render('admin.orders.invoice', [
            'order' => $order,
            'title' => 'Invoice #' . (int)($order['order_id'] ?? $id)
        ], 'admin');
    }

    public function exportOrders()
    {
        $rows = $this->db->fetchAll(
            "SELECT o.*, 
                    oi.product_id, oi.quantity, oi.price,
                    p.product_title
             FROM orders o
             LEFT JOIN order_items oi ON o.order_id = oi.order_id
             LEFT JOIN products p ON oi.product_id = p.product_id
             ORDER BY o.created_at DESC, o.order_id DESC"
        );

        $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $out = fopen('php://output', 'w');
        fputcsv($out, [
            'order_id','user_id','total_amount','order_status','payment_status','payment_method','shipping_address','created_at',
            'item_product_id','item_product_title','item_quantity','item_price'
        ]);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['order_id'] ?? '',
                $r['user_id'] ?? '',
                $r['total_amount'] ?? '',
                $r['order_status'] ?? '',
                $r['payment_status'] ?? '',
                $r['payment_method'] ?? '',
                $r['shipping_address'] ?? '',
                $r['created_at'] ?? '',
                $r['product_id'] ?? '',
                $r['product_title'] ?? '',
                $r['quantity'] ?? '',
                $r['price'] ?? '',
            ]);
        }
        fclose($out);
        exit;
    }

    public function users()
    {
        $search = $this->request->get('search');
        $users = $this->userModel->searchUsers($search);

        return $this->render('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'title' => 'Manage Users'
        ], 'admin');
    }

    public function viewUser($id)
    {
        $user = $this->userModel->find((int)$id);
        if (!$user) {
            $this->session->flash('error', 'User not found');
            return $this->redirect('/admin/users');
        }

        return $this->render('admin.users.view', [
            'user' => $user,
            'title' => 'User #' . (int)($user['id'] ?? $id)
        ], 'admin');
    }

    public function updateUser($id)
    {
        if (!$this->request->isPost()) {
            return $this->redirect('/admin/users/' . (int)$id);
        }

        $data = $this->request->post();

        $update = [
            'username' => trim($data['username'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'mobile' => trim($data['mobile'] ?? ''),
            'address' => trim($data['address'] ?? ''),
            'role' => trim($data['role'] ?? 'user'),
        ];

        if ($update['username'] === '' || $update['email'] === '') {
            $this->session->flash('error', 'Username and Email are required.');
            return $this->redirect('/admin/users/' . (int)$id);
        }

        if (!filter_var($update['email'], FILTER_VALIDATE_EMAIL)) {
            $this->session->flash('error', 'Invalid email address.');
            return $this->redirect('/admin/users/' . (int)$id);
        }

        if (!in_array($update['role'], ['user', 'admin'], true)) {
            $update['role'] = 'user';
        }

        $newPass = trim($data['new_password'] ?? '');
        if ($newPass !== '') {
            $update['password'] = password_hash($newPass, PASSWORD_DEFAULT);
        }

        try {
            $this->userModel->update((int)$id, $update);
            $this->session->flash('success', 'User updated successfully');
        } catch (\Throwable $e) {
            $this->session->flash('error', 'Failed to update user');
        }

        return $this->redirect('/admin/users/' . (int)$id);
    }

    public function deleteUser($id)
    {
        $id = (int)$id;
        $currentId = (int)($this->auth->id() ?? 0);

        if ($id === $currentId) {
            $this->session->flash('error', 'You cannot delete your own account while logged in.');
            return $this->redirect('/admin/users');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->session->flash('error', 'User not found');
            return $this->redirect('/admin/users');
        }

        $this->userModel->delete($id);
        $this->session->flash('success', 'User deleted');
        return $this->redirect('/admin/users');
    }

    public function userOrders($id)
    {
        $user = $this->userModel->find((int)$id);
        if (!$user) {
            $this->session->flash('error', 'User not found');
            return $this->redirect('/admin/users');
        }

        $orders = $this->orderModel->getUserOrders((int)$id);

        return $this->render('admin.users.orders', [
            'user' => $user,
            'orders' => $orders,
            'title' => 'Orders of ' . ($user['username'] ?? ('User #' . (int)$id))
        ], 'admin');
    }

    // --- Optional placeholder pages for routes that exist but are not built yet ---
    public function settings() { return $this->render('admin.placeholder', ['title' => 'Settings','message' => 'Settings page is not implemented yet.'], 'admin'); }
    public function updateSettings() { $this->session->flash('error','Not implemented'); return $this->redirect('/admin/settings'); }
    public function shippingSettings() { return $this->render('admin.placeholder', ['title' => 'Shipping Settings','message' => 'Not implemented yet.'], 'admin'); }
    public function updateShippingSettings() { $this->session->flash('error','Not implemented'); return $this->redirect('/admin/settings/shipping'); }
    public function paymentSettings() { return $this->render('admin.placeholder', ['title' => 'Payment Settings','message' => 'Not implemented yet.'], 'admin'); }
    public function updatePaymentSettings() { $this->session->flash('error','Not implemented'); return $this->redirect('/admin/settings/payment'); }
    public function salesReport() { return $this->render('admin.placeholder', ['title' => 'Sales Report','message' => 'Not implemented yet.'], 'admin'); }
    public function productsReport() { return $this->render('admin.placeholder', ['title' => 'Products Report','message' => 'Not implemented yet.'], 'admin'); }
    public function customersReport() { return $this->render('admin.placeholder', ['title' => 'Customers Report','message' => 'Not implemented yet.'], 'admin'); }
    public function generateReport() { $this->session->flash('error','Not implemented'); return $this->redirect('/admin/reports/sales'); }
    public function media() { return $this->render('admin.placeholder', ['title' => 'Media','message' => 'Not implemented yet.'], 'admin'); }
    public function uploadMedia() { $this->session->flash('error','Not implemented'); return $this->redirect('/admin/media'); }
    public function deleteMedia($id) { $this->session->flash('error','Not implemented'); return $this->redirect('/admin/media'); }
}
