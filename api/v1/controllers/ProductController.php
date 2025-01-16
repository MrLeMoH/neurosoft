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
        return ['products' => $products];
    }

    public function getProductById($id)
    {
        $product = $this->productModel->getById($id);
        return ['product' => $product];
    }

    public function updateProduct($id, $data)
    {
        return $this->productModel->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->productModel->delete($id);
    }

}
