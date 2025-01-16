<?php

namespace v1\controllers;

use v1\models\Category;
class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function createCategory($body)
    {
        return $this->categoryModel->create($body);
    }

    public function getAllCategories()
    {
        $categories = $this->categoryModel->getAll();
        echo json_encode(['categories' => $categories]);
    }

    public function getCategoryById($id)
    {
        $category = $this->categoryModel->getById($id);
        return ['category' => $category];
    }

    public function updateCategory($id, $data)
    {
        return $this->categoryModel->update($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->categoryModel->delete($id);
    }
}