<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    private $categoryModel;
    
    public function __construct($container)
    {
        parent::__construct($container);
        $this->categoryModel = new Category();
    }
    
    public function index()
    {
        $categories = $this->categoryModel->getWithProductCount();
        
        return $this->render('admin.categories.index', [
            'categories' => $categories,
            'title' => 'Manage Categories'
        ], 'admin');
    }
    
    public function create()
    {
        return $this->render('admin.categories.create', [
            'title' => 'Add New Category'
        ], 'admin');
    }
    
    public function store()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            // Validation
            $rules = [
                'category_title' => 'required|min:2|max:100',
                'description' => 'max:500',
                'meta_keywords' => 'max:255',
                'meta_description' => 'max:255'
            ];
            
            if ($this->validate($rules)) {
                $categoryData = [
                    'category_title' => $data['category_title'],
                    'description' => $data['description'] ?? '',
                    'meta_keywords' => $data['meta_keywords'] ?? '',
                    'meta_description' => $data['meta_description'] ?? '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => isset($data['status']) ? 1 : 0
                ];
                
                $categoryId = $this->categoryModel->create($categoryData);
                
                if ($categoryId) {
                    $this->session->flash('success', 'Category added successfully');
                    return $this->redirect('/admin/categories');
                }
            }
        }
        
        $this->session->flash('error', 'Failed to add category');
        return $this->redirect('/admin/categories/create');
    }
    
    public function edit($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $this->session->flash('error', 'Category not found');
            return $this->redirect('/admin/categories');
        }
        
        return $this->render('admin.categories.edit', [
            'category' => $category,
            'title' => 'Edit Category'
        ], 'admin');
    }
    
    public function update($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            $rules = [
                'category_title' => 'required|min:2|max:100',
                'description' => 'max:500'
            ];
            
            if ($this->validate($rules)) {
                $updateData = [
                    'category_title' => $data['category_title'],
                    'description' => $data['description'] ?? '',
                    'meta_keywords' => $data['meta_keywords'] ?? '',
                    'meta_description' => $data['meta_description'] ?? '',
                    'status' => isset($data['status']) ? 1 : 0
                ];
                
                if ($this->categoryModel->update($id, $updateData)) {
                    $this->session->flash('success', 'Category updated successfully');
                    return $this->redirect('/admin/categories');
                }
            }
        }
        
        $this->session->flash('error', 'Failed to update category');
        return $this->redirect('/admin/categories/' . $id . '/edit');
    }
    
    public function delete($id)
    {
        // Check if category has products
        $category = $this->categoryModel->find($id);
        
        if ($category) {
            $productCount = $this->categoryModel->getProducts($id);
            
            if (count($productCount) > 0) {
                $this->session->flash('error', 'Cannot delete category with products. Remove products first.');
            } else {
                if ($this->categoryModel->delete($id)) {
                    $this->session->flash('success', 'Category deleted successfully');
                } else {
                    $this->session->flash('error', 'Failed to delete category');
                }
            }
        } else {
            $this->session->flash('error', 'Category not found');
        }
        
        return $this->redirect('/admin/categories');
    }
}