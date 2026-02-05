<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Brand;

class BrandController extends Controller
{
    private $brandModel;
    
    public function __construct($container)
    {
        parent::__construct($container);
        $this->brandModel = new Brand();
    }
    
    public function index()
    {
        $brands = $this->brandModel->getWithProductCount();
        
        return $this->render('admin.brands.index', [
            'brands' => $brands,
            'title' => 'Manage Brands'
        ], 'admin');
    }
    
    public function create()
    {
        return $this->render('admin.brands.create', [
            'title' => 'Add New Brand'
        ], 'admin');
    }
    
    public function store()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            // Validation
            $rules = [
                'brand_title' => 'required|min:2|max:100',
                'description' => 'max:500'
            ];
            
            if ($this->validate($rules)) {
                $brandData = [
                    'brand_title' => $data['brand_title'],
                    'description' => $data['description'] ?? '',
                    'website' => $data['website'] ?? '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => isset($data['status']) ? 1 : 0
                ];
                
                $brandId = $this->brandModel->create($brandData);
                
                if ($brandId) {
                    $this->session->flash('success', 'Brand added successfully');
                    return $this->redirect('/admin/brands');
                }
            }
        }
        
        $this->session->flash('error', 'Failed to add brand');
        return $this->redirect('/admin/brands/create');
    }
    
    public function edit($id)
    {
        $brand = $this->brandModel->find($id);
        
        if (!$brand) {
            $this->session->flash('error', 'Brand not found');
            return $this->redirect('/admin/brands');
        }
        
        return $this->render('admin.brands.edit', [
            'brand' => $brand,
            'title' => 'Edit Brand'
        ], 'admin');
    }
    
    public function update($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            $rules = [
                'brand_title' => 'required|min:2|max:100',
                'description' => 'max:500'
            ];
            
            if ($this->validate($rules)) {
                $updateData = [
                    'brand_title' => $data['brand_title'],
                    'description' => $data['description'] ?? '',
                    'website' => $data['website'] ?? '',
                    'status' => isset($data['status']) ? 1 : 0
                ];
                
                if ($this->brandModel->update($id, $updateData)) {
                    $this->session->flash('success', 'Brand updated successfully');
                    return $this->redirect('/admin/brands');
                }
            }
        }
        
        $this->session->flash('error', 'Failed to update brand');
        return $this->redirect('/admin/brands/' . $id . '/edit');
    }
    
    public function delete($id)
    {
        $brand = $this->brandModel->find($id);
        
        if ($brand) {
            $productCount = $this->brandModel->getProducts($id);
            
            if (count($productCount) > 0) {
                $this->session->flash('error', 'Cannot delete brand with products. Remove products first.');
            } else {
                if ($this->brandModel->delete($id)) {
                    $this->session->flash('success', 'Brand deleted successfully');
                } else {
                    $this->session->flash('error', 'Failed to delete brand');
                }
            }
        } else {
            $this->session->flash('error', 'Brand not found');
        }
        
        return $this->redirect('/admin/brands');
    }
}