<?php
namespace Controllers;

use Models\Category;

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    public function createCategory() {
        $data = json_decode(file_get_contents('php://input'), true);
        // Здесь обработка создания категории
        $this->categoryModel->create($data);
        echo json_encode(['message' => 'Category created successfully']);
    }

    public function getAllCategories() {
        $categories = $this->categoryModel->getAll();
        echo json_encode(['categories' => $categories]);
    }

    public function getCategoryById($id) {
        $category = $this->categoryModel->getById($id);
        echo json_encode(['category' => $category]);
    }

    public function updateCategory($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->categoryModel->update($id, $data);
        echo json_encode(['message' => 'Category updated successfully']);
    }

    public function deleteCategory($id) {
        if ($this->categoryModel->hasProducts($id)) {
            echo json_encode(['error' => 'Cannot delete category with products']);
        } else {
            $this->categoryModel->delete($id);
            echo json_encode(['message' => 'Category deleted successfully']);
        }
    }
}
