<?php
// Конфигурация подключения
$host = 'localhost'; // Используй имя контейнера или сервиса из docker-compose.yml
$db = 'lash_reservation'; // Название базы данных
$user = 'root'; // Имя пользователя MySQL
$pass = 'Nemenit.123'; // Пароль

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    echo "Úspešne pripojené k databáze!";
} catch (PDOException $e) {
    echo "Chyba pripojenia: " . $e->getMessage();
}
?>