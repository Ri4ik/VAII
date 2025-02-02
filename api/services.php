<?php
// api/services.php
// Этот файл обрабатывает запросы на получение списка услуг.
require_once __DIR__ . '/Database.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
//header('Content-Type: application/json');

// Проверяем, что запрос был выполнен методом GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Подключение к базе данных
    $db = Database::getInstance()->getConnection();
    // Проверяем подключение
//    if ($db) {
//        echo json_encode(['success' => true, 'message' => 'Connection successful']);
//    } else {
//        throw new PDOException('Failed to establish database connection');
//    }
    // SQL-запрос для получения всех услуг
    $stmt = $db->query("SELECT id, name, price, duration FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //преобразует их в массив ассоциативных массивов, где каждый массив представляет одну строку из таблицы.

    // Возвращаем данные в формате JSON
    echo json_encode($services);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
