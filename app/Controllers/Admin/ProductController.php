<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\FileUploadService;

class ProductController extends Controller
{
    private $productModel;
    private $categoryModel;
    private $brandModel;
    private $uploadService;
    
    public function __construct($container)
    {
        parent::__construct($container);
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
        $this->uploadService = new FileUploadService();
    }
    
    public function index()
    {
        $products = $this->productModel->withCategory();
        
        return $this->render('admin/products/index', [
            'products' => $products,
            'title' => 'Manage Products'
        ], 'admin');
    }
    
    public function create()
    {
        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();
        
        return $this->render('admin/products/create', [
            'categories' => $categories,
            'brands' => $brands,
            'title' => 'Add Product'
        ], 'admin');
    }
    
    public function store()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $files = $this->request->file();
            
            // Validate
            $rules = [
                'product_title' => 'required|min:3|max:255',
                'product_description' => 'required',
                'product_price' => 'required|numeric|min:0',
                'category_id' => 'required|numeric',
                'brand_id' => 'required|numeric'
            ];
            
            if (!$this->validate($rules)) {
                return $this->redirect('/admin/products/create');
            }
            
            // Handle file uploads
            // FileUploadService::upload() returns an array of metadata; we only store the relative path
            // (e.g. "products/abc.png") in DB because the public views build URLs as /uploads/<path>.
            $image1 = $this->uploadService->upload($files['product_image1'], 'products');
            $image2 = $this->uploadService->upload($files['product_image2'], 'products');
            $image3 = $this->uploadService->upload($files['product_image3'], 'products');
            
            $productData = [
                'product_title' => $data['product_title'],
                'product_description' => $data['product_description'],
                'product_keywords' => $data['product_keywords'] ?? '',
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'],
                'product_image1' => $image1['path'] ?? null,
                'product_image2' => $image2['path'] ?? null,
                'product_image3' => $image3['path'] ?? null,
                'product_price' => $data['product_price'],
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ];
            
            $this->productModel->create($productData);
            
            $this->session->flash('success', 'Product added successfully');
            return $this->redirect('/admin/products');
        }
    }
    
    public function edit($id)
    {
        $product = $this->productModel->find($id);
        $categories = $this->categoryModel->all();
        $brands = $this->brandModel->all();
        
        return $this->render('admin/products/edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'title' => 'Edit Product'
        ], 'admin');
    }
    
    public function update($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $files = $this->request->file();

            $rules = [
                'product_title' => 'required|min:3|max:255',
                'product_description' => 'required',
                'product_price' => 'required|numeric|min:0',
                'category_id' => 'required|numeric',
                'brand_id' => 'required|numeric'
            ];

            if (!$this->validate($rules)) {
                return $this->redirect("/admin/products/{$id}/edit");
            }

            $updateData = [
                'product_title' => $data['product_title'],
                'product_description' => $data['product_description'],
                'product_keywords' => $data['product_keywords'] ?? '',
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'],
                'product_price' => $data['product_price'],
                'status' => isset($data['status']) ? (int)$data['status'] : 1
            ];

            // Optional image replacements
            try {
                if (!empty($files['product_image1']) && ($files['product_image1']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $img = $this->uploadService->upload($files['product_image1'], 'products');
                    $updateData['product_image1'] = $img['path'] ?? null;
                }
                if (!empty($files['product_image2']) && ($files['product_image2']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $img = $this->uploadService->upload($files['product_image2'], 'products');
                    $updateData['product_image2'] = $img['path'] ?? null;
                }
                if (!empty($files['product_image3']) && ($files['product_image3']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $img = $this->uploadService->upload($files['product_image3'], 'products');
                    $updateData['product_image3'] = $img['path'] ?? null;
                }
            } catch (\Throwable $e) {
                $this->session->flash('error', 'Image upload failed: ' . $e->getMessage());
                return $this->redirect("/admin/products/{$id}/edit");
            }

            $this->productModel->update($id, $updateData);

            $this->session->flash('success', 'Product updated successfully');
            return $this->redirect('/admin/products');
        }
    }

    public function delete($id)
    {
        $this->productModel->delete($id);
        $this->session->flash('success', 'Product deleted successfully');
        return $this->redirect('/admin/products');
    }


    public function export()
    {
        $products = $this->productModel->all();
        $filename = 'products_export_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $out = fopen('php://output', 'w');
        fputcsv($out, [
            'product_id','product_title','product_description','product_keywords',
            'category_id','brand_id','product_price','status','created_at'
        ]);

        foreach ($products as $p) {
            fputcsv($out, [
                $p['product_id'] ?? '',
                $p['product_title'] ?? '',
                $p['product_description'] ?? '',
                $p['product_keywords'] ?? '',
                $p['category_id'] ?? '',
                $p['brand_id'] ?? '',
                $p['product_price'] ?? '',
                $p['status'] ?? '',
                $p['created_at'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    public function import()
    {
        if (!$this->request->isPost()) {
            return $this->redirect('/admin/products');
        }

        $file = $this->request->file('csv_file');
        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->session->flash('error', 'Please upload a CSV file.');
            return $this->redirect('/admin/products');
        }

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            $this->session->flash('error', 'Only .csv files are supported.');
            return $this->redirect('/admin/products');
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            $this->session->flash('error', 'Cannot read uploaded file.');
            return $this->redirect('/admin/products');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $this->session->flash('error', 'Empty CSV.');
            return $this->redirect('/admin/products');
        }

        // Normalize header
        $header = array_map(function($h){ return strtolower(trim($h)); }, $header);

        $inserted = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = [];
            foreach ($header as $i => $col) {
                $data[$col] = $row[$i] ?? null;
            }

            // Required fields
            $title = trim((string)($data['product_title'] ?? ''));
            $desc  = trim((string)($data['product_description'] ?? ''));
            $price = $data['product_price'] ?? null;
            $categoryId = (int)($data['category_id'] ?? 0);
            $brandId = (int)($data['brand_id'] ?? 0);

            if ($title === '' || $desc === '' || !is_numeric($price) || $categoryId <= 0 || $brandId <= 0) {
                $skipped++;
                continue;
            }

            $productData = [
                'product_title' => $title,
                'product_description' => $desc,
                'product_keywords' => (string)($data['product_keywords'] ?? ''),
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                // images are optional in import
                'product_image1' => '',
                'product_image2' => '',
                'product_image3' => '',
                'product_price' => (float)$price,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => (int)($data['status'] ?? 1) ? 1 : 0
            ];

            try {
                $this->productModel->create($productData);
                $inserted++;
            } catch (\Throwable $e) {
                $skipped++;
            }
        }

        fclose($handle);

        $this->session->flash('success', "Imported {$inserted} products. Skipped {$skipped} rows.");
        return $this->redirect('/admin/products');
    }

}
