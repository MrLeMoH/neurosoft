<?php

namespace v1\controllers;

use v1\models\Product;

class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function createProduct($body)
    {
       return $this->productModel->create($body);
    }

    public function getAllProducts()
    {
        $products = $this->productModel->getAll();
        echo json_encode(['products' => $products]);
    }

    public function getProductsByCategory($categoryId = null)
    {
        $products = $this->productModel->getByCategory($categoryId);
        return json_encode(['products' => $products]);
    }

    public function getProductById($id)
    {
        $product = $this->productModel->getById($id);
        echo json_encode(['product' => $product]);
    }

    public function updateProduct($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->productModel->update($id, $data);
        echo json_encode(['message' => 'Product updated successfully']);
    }

    public function deleteProduct($id)
    {
        $this->productModel->delete($id);
        echo json_encode(['message' => 'Product deleted successfully']);
    }
}
