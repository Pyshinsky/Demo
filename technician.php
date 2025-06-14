<?php
session_start();
include('Config.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
    header("Location: index.php");
    exit;
}

// Обновление статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE conference_orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['new_status'], $_POST['order_id']]);
}

// Получение заказов
$orders = $pdo->query("SELECT * FROM conference_orders WHERE status IN ('принят', 'готовится')")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Техник</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Техник: <?= $_SESSION['username'] ?></h2>
    <a href="logout.php">Выйти</a>

    <h3>Заказы</h3>
    <table class="table table-bordered">
        <tr><th>ID</th><th>Конференция</th><th>Гости</th><th>Оборудование</th><th>Статус</th><th>Изменить статус</th></tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id']

