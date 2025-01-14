<?php
// api/services.php
// Этот файл обрабатывает запросы на получение списка услуг.

header('Content-Type: application/json');

// Проверяем, что запрос был выполнен методом GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Подключение к базе данных
    $pdo = new PDO("mysql:host=localhost;dbname=lash_reservation", "root", "Nemenit.123");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL-запрос для получения всех услуг
    $stmt = $pdo->query("SELECT id, name, price, duration FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //преобразует их в массив ассоциативных массивов, где каждый массив представляет одну строку из таблицы.

    // Возвращаем данные в формате JSON
    echo json_encode($services);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
