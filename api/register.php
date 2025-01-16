<?php
header('Content-Type: application/json');

// Логирование метода запроса
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Логирование данных запроса
$input = file_get_contents("php://input");
error_log("Raw Input Data: " . $input);

// Пробуем декодировать JSON
$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode failed: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

// Проверяем наличие всех обязательных полей
if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
    error_log("Missing fields in JSON: " . json_encode($data));
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Возвращаем успешный ответ для теста
echo json_encode(['success' => true, 'message' => 'User registered successfully']);
