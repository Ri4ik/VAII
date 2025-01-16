<?php
//// Конфигурация подключения
//$host = 'database'; // Используй имя контейнера или сервиса из docker-compose.yml
//$db = 'lash_reservation'; // Название базы данных
//$user = 'root'; // Имя пользователя MySQL
//$pass = 'Nemenit.123'; // Пароль
require_once __DIR__ . '/Database.php';
try {
    $db = Database::getInstance()->getConnection();
//    $config = include(__DIR__ . '/../config.php');
//    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['user'], $config['password']);
//    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Úspešne pripojené k databáze!";
} catch (PDOException $e) {
    echo "Chyba pripojenia: " . $e->getMessage();
}
?>