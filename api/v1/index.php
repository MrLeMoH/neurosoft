<?php
// Подключаем модели и контроллеры
use v1\models\User;

require_once __DIR__ . '/models/Database.php'; // Подключение модели базы данных
require_once __DIR__ . '/models/User.php'; // Подключение модели пользователя
require_once __DIR__ . '/models/Category.php'; // Подключение модели категории
require_once __DIR__ . '/models/Product.php'; // Подключение модели продукта
require_once __DIR__ . '/controllers/ProductController.php'; // Подключение контроллера для работы с продуктами
require_once __DIR__ . '/controllers/CategoryController.php'; // Подключение контроллера для работы с категориями
require_once __DIR__ . '/controllers/AuthController.php'; // Подключение контроллера для аутентификации

// Получаем параметры запроса
$method = $_SERVER['REQUEST_METHOD']; // Определяем метод запроса (GET, POST, PATCH, DELETE)
$uri = explode('/', trim($_SERVER['REDIRECT_URL'], '/')); // Разделяем URI на части
header('Content-Type: application/json'); // Устанавливаем правильные заголовки для JSON-ответа

// Проверка на наличие контроллера в запросе
if (isset($uri[1])) {
    switch ($uri[3]) {
        case 'categories': // Работа с категориями
            $categoryController = new v1\controllers\CategoryController();

            if ($method === 'POST') {
                // Проверка авторизации
                $user = new User();
                if (!$user->isAuth()) {
                    http_response_code(403); // Код 403 — доступ запрещен
                    echo json_encode(['error' => 'no access']);
                    exit;
                }

                // Создание категории
                $body = json_decode(file_get_contents('php://input'), true);

                // Проверка на наличие обязательного поля
                if (empty($body['name'])) {
                    http_response_code(400); // Код 400 — отсутствует обязательное поле
                    echo json_encode(['error' => 'Missing required field: name']);
                    exit;
                }

                // Попытка создать категорию
                if ($categoryController->createCategory($body)) {
                    echo json_encode(['message' => 'Category created successfully']);
                    exit;
                } else {
                    http_response_code(500); // Код 500 — ошибка сервера
                    echo json_encode(['error' => 'Error creating category']);
                    exit;
                }
            } elseif ($method === 'GET') {
                // Получение категорий
                if (!empty($uri[4])) {
                    // Получение категории по ID
                    $category = $categoryController->getCategoryById($uri[4]);
                    if ($category) {
                        echo json_encode(['category' => $category]);
                    } else {
                        http_response_code(404); // Код 404 — не найдено
                        echo json_encode(['error' => 'Category not found']);
                    }
                    exit;
                } else {
                    // Получение всех категорий
                    $categories = $categoryController->getAllCategories();
                    echo json_encode(['categories' => $categories]);
                    exit;
                }
            } elseif ($method === 'PATCH') {
                // Проверка авторизации
                $user = new User();
                if (!$user->isAuth()) {
                    http_response_code(403); // Код 403 — доступ запрещен
                    echo json_encode(['error' => 'no access']);
                    exit;
                }

                // Обновление категории
                if (empty($uri[4])) {
                    http_response_code(400); // Код 400 — отсутствует ID
                    echo json_encode(['error' => 'Missing required fields: id is empty']);
                    exit;
                }
                $body = json_decode(file_get_contents('php://input'), true);

                // Проверка на наличие обязательных полей
                if (empty($body['name'])) {
                    http_response_code(400); // Код 400 — отсутствует обязательное поле
                    echo json_encode(['error' => 'Missing required field: name']);
                    exit;
                }

                // Обновление категории
                if ($categoryController->updateCategory($uri[4], $body)) {
                    echo json_encode(['message' => 'Category updated successfully']);
                } else {
                    http_response_code(500); // Код 500 — ошибка сервера
                    echo json_encode(['error' => 'Error updating category']);
                }
            } elseif ($method === 'DELETE') {
                // Проверка авторизации
                $user = new User();
                if (!$user->isAuth()) {
                    http_response_code(403); // Код 403 — доступ запрещен
                    echo json_encode(['error' => 'no access']);
                    exit;
                }

                // Удаление категории
                if (empty($uri[4])) {
                    http_response_code(400); // Код 400 — отсутствует ID
                    echo json_encode(['error' => 'Missing required fields: id is empty']);
                    exit;
                }

                // Попытка удалить категорию
                if ($categoryController->deleteCategory($uri[4])) {
                    http_response_code(200); // Код 200 — успешное удаление
                    echo json_encode(['message' => 'Category deleted successfully']);
                    exit;
                } else {
                    http_response_code(500); // Код 500 — ошибка сервера
                    echo json_encode(['error' => 'Error deleting category']);
                    exit;
                }
            }
            break;
        case 'products': // Работа с продуктами
            $productController = new v1\controllers\ProductController();

            if ($method === 'POST') {
                // Проверка авторизации
                $user = new User();
                if (!$user->isAuth()) {
                    http_response_code(403); // Код 403 — доступ запрещен
                    echo json_encode(['error' => 'no access']);
                    exit;
                }

                // Создание продукта
                $body = json_decode(file_get_contents('php://input'), true);

                // Проверка обязательных полей
                if (empty($body['name']) || empty($body['categoryId'])) {
                    http_response_code(400); // Код 400 — отсутствует обязательное поле
                    echo json_encode(['error' => 'Missing required fields: name or categoryId']);
                    exit;
                }

                // Попытка создать продукт
                if ($productController->createProduct($body)) {
                    echo json_encode(['message' => 'Product created successfully']);
                } else {
                    http_response_code(500); // Код 500 — ошибка сервера
                    echo json_encode(['error' => 'Error creating product']);
                }
            } elseif ($method === 'GET') {
                // Получение продуктов
                if (!empty($uri[4])) {
                    // Получение продукта по ID
                    $product = $productController->getProductById($uri[4]);
                    if ($product) {
                        echo json_encode(['product' => $product]);
                    } else {
                        http_response_code(404); // Код 404 — не найдено
                        echo json_encode(['error' => 'Product not found']);
                    }
                    exit;
                }

                // Получение продуктов по категории, если параметр 'category' передан в GET-запросе
                $category = null;
                if (!empty($_GET['category'])) {
                    $category = $_GET['category'];
                }
                $products = $productController->getProductsByCategory($category);
                if ($products) {
                    echo json_encode(['product' => $products]);
                } else {
                    http_response_code(404); // Код 404 — не найдено
                    echo json_encode(['error' => 'Product not found']);
                }
            } elseif ($method === 'PATCH') {
                // Проверка авторизации
                $user = new User();
                if (!$user->isAuth()) {
                    http_response_code(403); // Код 403 — доступ запрещен
                    echo json_encode(['error' => 'no access']);
                    exit;
                }

                // Обновление продукта
                if (empty($uri[4])) {
                    http_response_code(400); // Код 400 — отсутствует ID
                    echo json_encode(['error' => 'Missing required fields: id is empty']);
                    exit;
                }

                $body = json_decode(file_get_contents('php://input'), true);

                // Проверка обязательных полей
                if (empty($body['name']) && empty($body['categoryId']) && empty($body['status'])) {
                    http_response_code(400); // Код 400 — отсутствуют обязательные поля
                    echo json_encode(['error' => 'Missing required fields: name, categoryId, or status']);
                    exit;
                }

                // Обновление продукта
                if ($productController->updateProduct($uri[4], $body)) {
                    echo json_encode(['message' => 'Product updated successfully']);
                    exit;
                } else {
                    http_response_code(500); // Код 500 — ошибка сервера
                    echo json_encode(['error' => 'Error updating product']);
                    exit;
                }
            } elseif ($method === 'DELETE') {
                // Проверка авторизации
                $user = new User();
                if (!$user->isAuth()) {
                    http_response_code(403); // Код 403 — доступ запрещен
                    echo json_encode(['error' => 'no access']);
                    exit;
                }

                // Удаление продукта
                if (empty($uri[4])) {
                    http_response_code(400); // Код 400 — отсутствует ID
                    echo json_encode(['error' => 'Missing required fields: id is empty']);
                    exit;
                }

                // Попытка удалить продукт
                if ($productController->deleteProduct($uri[4])) {
                    http_response_code(200); // Код 200 — успешное удаление
                    echo json_encode(['message' => 'Product deleted successfully']);
                    exit;
                } else {
                    http_response_code(500); // Код 500 — ошибка сервера
                    echo json_encode(['error' => 'Error deleting product']);
                    exit;
                }
            }
            break;
        case 'auth': // Работа с аутентификацией
            $authController = new v1\controllers\AuthController();

            if ($method === 'POST') {
                // Авторизация пользователя
                $body = json_decode(file_get_contents('php://input'), true);

                // Проверка обязательных полей
                if (empty($body['login']) || empty($body['password'])) {
                    http_response_code(400); // Код 400 — отсутствует обязательное поле
                    echo json_encode(['error' => 'Missing required fields: username or password']);
                    exit;
                }

                // Попытка авторизации
                echo json_encode($authController->authenticate($body));
            } elseif ($method === 'DELETE') {
                // Выход из учетной записи
                echo json_encode($authController->logout());
                exit;
            } elseif ($method === 'GET') {
                // Получение текущего авторизованного пользователя
                echo json_encode($authController->getCurrentUser());
                exit;
            }
            break;
        default:
            // Неизвестный контроллер
            http_response_code(404); // Код 404 — не найдено
            echo json_encode(['error' => 'Unknown controller']);
            break;
    }
} else {
    // Контроллер не указан
    http_response_code(400); // Код 400 — некорректный запрос
    echo json_encode(['error' => 'No controller specified']);
}
