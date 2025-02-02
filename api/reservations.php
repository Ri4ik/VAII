<?php
require_once __DIR__ . '/Database.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Проверяем метод запроса
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance()->getConnection();

    if ($requestMethod === 'POST') {
        // *** CREATE: Добавление резервации ***
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name'], $data['email'], $data['service'], $data['date'], $data['time'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $name = trim($data['name']);
        $email = trim($data['email']);
        $service = $data['service'];
        $date = $data['date'];
        $time = $data['time'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        // Проверка и создание пользователя
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $user_id = $user['id'] ?? null;

        if (!$user_id) {
            $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user_id = $db->lastInsertId();
        }

        // Проверяем пересечение времени
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM reservations 
            WHERE service_id = :service 
              AND reservation_date = :date 
              AND reservation_time = :time
        ");
        $stmt->bindParam(':service', $service);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'The selected time slot is already reserved']);
            exit;
        }

        // Добавляем резервацию
        $stmt = $db->prepare("
            INSERT INTO reservations (user_id, service_id, reservation_date, reservation_time) 
            VALUES (:user_id, :service, :date, :time)
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':service', $service);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Reservation added successfully']);
        } else {
            throw new PDOException('Failed to add reservation');
        }
    } elseif ($requestMethod === 'GET') {
        // *** READ: Получение всех резерваций ***
        $stmt = $db->query("
            SELECT r.id, u.name as user_name, u.email, s.name as service_name, 
                   r.reservation_date, r.reservation_time
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN services s ON r.service_id = s.id
            ORDER BY r.reservation_date, r.reservation_time
        ");
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'reservations' => $reservations]);
    } elseif ($requestMethod === 'PUT') {
        // *** UPDATE: Обновление резервации ***
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'], $data['service'], $data['date'], $data['time'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $id = $data['id'];
        $service = $data['service'];
        $date = $data['date'];
        $time = $data['time'];

        // Обновляем резервацию
        $stmt = $db->prepare("
            UPDATE reservations 
            SET service_id = :service, reservation_date = :date, reservation_time = :time
            WHERE id = :id
        ");
        $stmt->bindParam(':service', $service);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Reservation updated successfully']);
        } else {
            throw new PDOException('Failed to update reservation');
        }
    } elseif ($requestMethod === 'DELETE') {
        // *** DELETE: Удаление резервации ***
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing reservation ID']);
            exit;
        }

        $id = $data['id'];

        $stmt = $db->prepare("DELETE FROM reservations WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Reservation deleted successfully']);
        } else {
            throw new PDOException('Failed to delete reservation');
        }
    } else {
        // Неподдерживаемый метод
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}