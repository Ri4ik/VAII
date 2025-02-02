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
$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance()->getConnection();

    if ($method === 'GET') {
        // Получить все отзывы
        $stmt = $db->query("SELECT reviews.id, users.name, reviews.review_text, reviews.created_at
                            FROM reviews
                            JOIN users ON reviews.user_id = users.id
                            ORDER BY reviews.created_at DESC");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($reviews);

    } elseif ($method === 'POST') {
        // Добавить новый отзыв
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['user_id']) || !isset($data['review_text'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO reviews (user_id, review_text) VALUES (:user_id, :review_text)");
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':review_text', $data['review_text']);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Review added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add review']);
        }

    } elseif ($method === 'DELETE') {
        // Удалить отзыв
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing review ID']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id']);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete review']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
