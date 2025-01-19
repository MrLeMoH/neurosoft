<?php
namespace v1\controllers;

use v1\models\Category;

class CategoryController
{
    private $categoryModel;

    // Конструктор контроллера, создаем объект модели Category
    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    /**
     * Создание новой категории
     *
     * @param array $body - Данные категории для создания
     * @return bool - Возвращает true, если категория была создана успешно
     */
    public function createCategory($body)
    {
        // Передаем данные модели для создания новой категории
        return $this->categoryModel->create($body);
    }

    /**
     * Получение всех категорий
     *
     * @return array - Возвращает данные категории по ID
     */
    public function getAllCategories()
    {
        // Получаем список всех категорий
        return $this->categoryModel->getAll();
    }

    /**
     * Получение категории по ID
     *
     * @param int $id - ID категории
     * @return array - Возвращает данные категории по ID
     */
    public function getCategoryById($id)
    {
        // Получаем данные категории по ID
        return $this->categoryModel->getById($id);
    }

    /**
     * Обновление данных категории
     *
     * @param int $id - ID категории для обновления
     * @param array $data - Данные для обновления категории
     * @return bool - Возвращает true, если категория была обновлена успешно
     */
    public function updateCategory($id, $data)
    {
        // Передаем данные модели для обновления категории
        return $this->categoryModel->update($id, $data);
    }

    /**
     * Удаление категории
     *
     * @param int $id - ID категории для удаления
     * @return bool - Возвращает true, если категория была удалена успешно
     */
    public function deleteCategory($id)
    {
        // Передаем ID категории модели для удаления
        return $this->categoryModel->delete($id);
    }
}
