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
            $categoryController = new v1\controllers\CategoryController();

            if ($method === 'POST') {
                $body = json_decode(file_get_contents('php://input'), true);
                if (empty($body['name'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing required field: name']);
                    exit;
                }

                if ($categoryController->createCategory($body)) {
                    echo json_encode(['message' => 'Category created successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error creating category']);
                }
            } elseif ($method === 'GET') {
                if (!empty($uri[4])) {
                    echo json_encode($categoryController->getCategoryById($uri[4]));
                } else {
                    echo json_encode($categoryController->getAllCategories());
                }
            } elseif ($method === 'PATCH') {
                if (empty($uri[4])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing required fields: id is empty']);
                    exit;
                }
                $body = json_decode(file_get_contents('php://input'), true);
                if (empty($body['name'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing required field: name']);
                    exit;
                }

                if ($categoryController->updateCategory($uri[4], $body)) {
                    echo json_encode(['message' => 'Category updated successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error updating category']);
                }
            } elseif ($method === 'DELETE') {
                if (empty($uri[4])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing required fields: id is empty']);
                    exit;
                }

                if ($categoryController->deleteCategory($uri[4])) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Category deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error deleting category']);
                }
            }
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
                if (!empty($uri[4]))
                {
                    echo json_encode($productController->getProductById($uri[4]));
                    exit();
                }

                $category = null;
                if (!empty($_GET['category']))
                {
                    $category = $_GET['category'];
                }
                echo json_encode($productController->getProductsByCategory($category));
            }
            elseif ($method === "PATCH")
            {
                if (empty($uri[4]))
                {
                    // Ответ с кодом ошибки 400 (Bad Request) и сообщением
                    http_response_code(400);  // Код ответа 400 — неверный запрос
                    echo json_encode(['error' => 'Missing required fields: id is empty']);
                    exit;  // Останавливаем выполнение скрипта
                }
                // Чтение тела запроса
                $body = json_decode(file_get_contents('php://input'), true);

                if (empty($body["name"]) && empty($body["categoryId"]) && empty($body["status"]) ) {
                    // Ответ с кодом ошибки 400 (Bad Request) и сообщением
                    http_response_code(400);  // Код ответа 400 — неверный запрос
                    echo json_encode(['error' => 'Missing required fields: name and categoryId and status is empty']);
                    exit;  // Останавливаем выполнение скрипта
                }

                if ($productController->updateProduct($uri[4], $body)) {
                    echo json_encode(['message' => 'Product updated successfully']);
                }
                else
                {
                    echo json_encode(['error' => 'Error updating product']);
                }
            }
            elseif ($method === "DELETE")
            {
                if (empty($uri[4])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing required fields: id is empty']);
                    exit;
                }

                $id = $uri[4];
                if ($productController->deleteProduct($id)) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Product deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error deleting product']);
                }
            }
            break;
        case 'auth':
            $authController = new v1\controllers\AuthController();

            if ($method === 'POST') {
                // Аутентификация пользователя
                if (empty($uri[2])) {
                    $body = json_decode(file_get_contents('php://input'), true);
                    if (empty($body['username']) || empty($body['password'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing required fields: username or password']);
                        exit;
                    }

                    echo json_encode($authController->authenticate($body));
                }
            } elseif ($method === 'DELETE') {
                // Выход из учетной записи (сброс аутентификации)
                echo json_encode($authController->logout());
            } elseif ($method === 'GET') {
                // Получение данных текущего авторизованного пользователя
                echo json_encode($authController->getCurrentUser());
            }
            break;

        default:
            echo json_encode(['error' => 'Unknown controller']);
            break;
    }
} else {
    echo json_encode(['error' => 'No controller specified']);
}
