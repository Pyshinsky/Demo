<?php
// Конфигурация подключения к базе данных
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'denis');
define('DB_PASSWORD', '1234');
define('DB_DATABASE', 'demo');
define('DB_PORT', 3306);

// Соединение с базой данных
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE . ";port=" . DB_PORT, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>