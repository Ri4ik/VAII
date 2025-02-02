<?php
require_once __DIR__ . '/Database.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id']) || empty($input['reservation_date']) || empty($input['reservation_time'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        UPDATE reservations
        SET reservation_date = :reservation_date, reservation_time = :reservation_time
        WHERE id = :id
    ");
    $stmt->execute([
        ':id' => $input['id'],
        ':reservation_date' => $input['reservation_date'],
        ':reservation_time' => $input['reservation_time'],
    ]);

    echo json_encode(['success' => true, 'message' => 'Reservation updated']);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
