<?php
namespace v1\controllers;

use v1\models\Product;

class ProductController
{
    private $productModel;

    // Конструктор контроллера, создаем объект модели Product
    public function __construct()
    {
        $this->productModel = new Product();
    }

    /**
     * Создание нового продукта
     *
     * @param array $body - Данные продукта для создания
     * @return bool - Возвращает true, если продукт был создан успешно
     */
    public function createProduct($body)
    {
        // Передаем данные модели для создания нового продукта
        return $this->productModel->create($body);
    }

    /**
     * Получение продуктов по категории
     *
     * @param int|null $categoryId - ID категории для фильтрации
     * @return array - Возвращает список продуктов для указанной категории
     */
    public function getProductsByCategory($categoryId = null)
    {
        // Получаем список продуктов по категории
        return $this->productModel->getByCategory($categoryId);
    }

    /**
     * Получение продукта по его ID
     *
     * @param int $id - ID продукта
     * @return array - Возвращает данные одного продукта по ID
     */
    public function getProductById($id)
    {
        // Получаем данные продукта по ID
        return $this->productModel->getById($id);
    }

    /**
     * Обновление данных продукта
     *
     * @param int $id - ID продукта для обновления
     * @param array $data - Данные для обновления продукта
     * @return bool - Возвращает true, если продукт был обновлен успешно
     */
    public function updateProduct($id, $data)
    {
        // Передаем данные модели для обновления продукта
        return $this->productModel->update($id, $data);
    }

    /**
     * Удаление продукта
     *
     * @param int $id - ID продукта для удаления
     * @return bool - Возвращает true, если продукт был удален успешно
     */
    public function deleteProduct($id)
    {
        // Передаем ID продукта модели для удаления
        return $this->productModel->delete($id);
    }
}

