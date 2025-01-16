<?php
// api/invitations.php
require_once __DIR__ . '/Database.php';
header('Content-Type: application/json');

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Получаем данные из POST-запроса
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['inviter_id'], $data['invitee_email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$inviterId = $data['inviter_id'];
$inviteeEmail = $data['invitee_email'];

try {
    $db = Database::getInstance()->getConnection();

    // Генерация уникального токена для версии PHP ниже 7.0
    $token = sha1(uniqid(mt_rand(), true)); // Альтернатива random_bytes()

    // Добавление приглашения в базу данных
    $stmt = $db->prepare("INSERT INTO invitations (inviter_id, invitee_email, token) VALUES (:inviter_id, :invitee_email, :token)");
    $stmt->bindParam(':inviter_id', $inviterId);
    $stmt->bindParam(':invitee_email', $inviteeEmail);
    $stmt->bindParam(':token', $token);

    if ($stmt->execute()) {
        $inviteLink = "http://localhost:8000/invite.html?token=$token";
        echo json_encode(['success' => true, 'invite_link' => $inviteLink]);
    } else {
        throw new Exception("Failed to create invitation");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
