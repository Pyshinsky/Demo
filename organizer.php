<?php
session_start();
include('Config.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Создание заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $stmt = $pdo->prepare("INSERT INTO conference_orders (conference_name, guests_count, equipment, status, created_by)
                           VALUES (?, ?, ?, 'не принят', ?)");
    $equipment = implode(', ', $_POST['equipment']);
    $stmt->execute([$_POST['conference_name'], $_POST['guests_count'], $equipment, $user_id]);
}

// Получение заказов текущего пользователя
$orders = $pdo->prepare("SELECT * FROM conference_orders WHERE created_by = ?");
$orders->execute([$user_id]);
$orders = $orders->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Организатор</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Организатор: <?= $_SESSION['username'] ?></h2>
    <a href="logout.php">Выйти</a>

    <h3>Создать заказ</h3>
    <form method="POST">
        Название конференции: <input type="text" name="conference_name" required><br>
        Количество гостей: <input type="number" name="guests_count" required><br>
        Оборудование:
        <select name="equipment[]" multiple required>
            <option value="Проектор">Проектор</option>
            <option value="Микрофон">Микрофон</option>
            <option value="Экран">Экран</option>
        </select><br>
        <button type="submit" name="create_order">Создать</button>
    </form>

    <h3>Мои заказы</h3>
    <table class="table table-bordered">
        <tr><th>ID</th><th>Название</th><th>Гости</th><th>Оборудование</th><th>Статус</th></tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= $order['conference_name'] ?></td>
            <td><?= $order['guests_count'] ?></td>
            <td><?= $order['equipment'] ?></td>
            <td><?= $order['status'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
