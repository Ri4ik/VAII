<?php

class Database {
    private static $instance = null; // Единственный экземпляр
    private $connection; // Хранение подключения

    private function __construct() {
        // Загружаем конфигурацию
        $config = include(__DIR__ . '/../config.php');
        try {
            $this->connection = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8",
                $config['user'],
                $config['password']
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    // Запрещаем клонирование объекта
    private function __clone() {}

    // Запрещаем десериализацию объекта
    public function __wakeup() {}

    // Метод для получения единственного экземпляра
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Метод для получения соединения
    public function getConnection() {
        return $this->connection;
    }
}
