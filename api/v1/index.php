<?php
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/CategoryController.php';
require_once __DIR__ . '/controllers/AuthController.php';

// Получаем параметры запроса
$method = $_SERVER['REQUEST_METHOD']; // Получаем метод запроса (GET, POST, PATCH, DELETE)
$uri = explode('/', trim($_SERVER['REDIRECT_URL'], '/')); // Разделяем URI
// Роутинг для контроллеров и методов
if (isset($uri[1])) {
    switch ($uri[3]) {
        case 'categories':
            var_dump("categories");
//            $categoryController = new Controllers\CategoryController();
//            if ($method === 'POST' && count($uri) === 3) {
//                $categoryController->createCategory();
//            } elseif ($method === 'GET' && count($uri) === 3) {
//                $categoryController->getCategoryById($uri[2]);
//            } elseif ($method === 'GET' && count($uri) === 2) {
//                $categoryController->getAllCategories();
//            } elseif ($method === 'PATCH' && count($uri) === 3) {
//                $categoryController->updateCategory($uri[2]);
//            } elseif ($method === 'DELETE' && count($uri) === 3) {
//                $categoryController->deleteCategory($uri[2]);
//            }
            break;

        case 'products':
            $productController = new v1\controllers\ProductController();
            if ($method === "POST") {
                // Чтение тела запроса
                $body = json_decode(file_get_contents('php://input'), true);

                // Проверка на пустые значения
                if (empty($body["name"]) || empty($body["categoryId"])) {
                    // Ответ с кодом ошибки 400 (Bad Request) и сообщением
                    http_response_code(400);  // Код ответа 400 — неверный запрос
                    echo json_encode(['error' => 'Missing required fields: name or categoryId']);
                    exit;  // Останавливаем выполнение скрипта
                }


                if ($productController->createProduct($body)) {
                    echo json_encode(['message' => 'Product created successfully']);
                } else {
                    echo json_encode(['error' => 'Error creating product']);
                }
                exit;
            } elseif ($method === "GET") {
                $category = $_GET['category'];
                if (empty($_GET['category']))
                {
                    $category = null;
                }
                echo $productController->getProductsByCategory($category);
            }
//            $productController = new v1\controllers\ProductController();
//            if ($method === 'POST' && count($uri) === 3) {
//                $productController->createProduct();
//            } elseif ($method === 'GET' && count($uri) === 3) {
//                $productController->getProductById($uri[2]);
//            } elseif ($method === 'GET' && count($uri) === 2) {
//                if (isset($_GET['category'])) {
//                    $productController->getProductsByCategory($_GET['category']);
//                } else {
//                    $productController->getAllProducts();
//                }
//            } elseif ($method === 'PATCH' && count($uri) === 3) {
//                $productController->updateProduct($uri[2]);
//            } elseif ($method === 'DELETE' && count($uri) === 3) {
//                $productController->deleteProduct($uri[2]);
//            }
            break;

        case 'auth':
            var_dump("auth");
//            $authController = new Controllers\AuthController();
//            if ($method === 'POST') {
//                $authController->authenticate();
//            } elseif ($method === 'DELETE') {
//                $authController->logout();
//            } elseif ($method === 'GET') {
//                $authController->getCurrentUser();
//            }
            break;

        default:
            echo json_encode(['error' => 'Unknown controller']);
            break;
    }
} else {
    echo json_encode(['error' => 'No controller specified']);
}
