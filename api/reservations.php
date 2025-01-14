<?php
// Обработка POST-запроса для добавления резервации
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    // Получаем данные из запроса
    $data = json_decode(file_get_contents("php://input"), true);

    // Валидация данных
    if (isset($data['name']) && isset($data['email']) && isset($data['service']) && isset($data['date']) && isset($data['time'])) {
        $name = $data['name'];
        $email = $data['email'];
        $service = $data['service'];
        $date = $data['date'];
        $time = $data['time'];

        // Подключение к базе данных
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=lash_reservation", "root", "Nemenit.123");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Проверяем, существует ли уже пользователь с таким email
            $sql = "SELECT id FROM users WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                // Если пользователя нет, создаем нового
                $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                // Получаем id нового пользователя
                $user_id = $pdo->lastInsertId();
            } else {
                // Если пользователь есть, берем его id
                $user_id = $user['id'];
            }

            // SQL-запрос для добавления резервации
            $sql = "INSERT INTO reservations (user_id, service_id, reservation_date, reservation_time) 
                    VALUES (:user_id, :service, :date, :time)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':service', $service);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);

            // Выполнение запроса
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Reservation added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add reservation']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
}

?>