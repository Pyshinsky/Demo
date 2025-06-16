<?php
session_start();
include('Config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Удаление заявки (только для админа)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $_SESSION['role'] === 'admin') {
    $stmt = $pdo->prepare("DELETE FROM requests WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
}

// Получение заявок с правильными названиями столбцов
$query = "SELECT r.id, 
                 a.auto_name, 
                 c_from.name_city AS city_from, 
                 c_to.name_city AS city_to, 
                 r.date_start, 
                 r.date_finish
          FROM requests r
          JOIN index_auto a ON r.auto_id = a.id
          JOIN index_city c_from ON r.city_from_id = c_from.id_city
          JOIN index_city c_to ON r.city_to_id = c_to.id_city";

// Для обычных пользователей - только их заявки
if ($_SESSION['role'] !== 'admin') {
    $query .= " WHERE r.created_by = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->query($query);
}
$requests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Логистические заявки</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Заявки: <?= $_SESSION['username'] ?></h2>
    <a href="logout.php">Выйти</a> 
    <?php if ($_SESSION['role'] === 'admin'): ?>
        | <a href="admin.php">Админ панель</a>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h3>Активные заявки</h3>
        <a href="create_request.php" class="btn btn-primary">Создать заявку</a>
    </div>
    
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Транспорт</th>
                <th>Отправление</th>
                <th>Назначение</th>
                <th>Дата отправки</th>
                <th>Дата прибытия</th>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <th>Действия</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
            <tr>
                <td><?= $request['auto_name'] ?></td>
                <td><?= $request['city_from'] ?></td>
                <td><?= $request['city_to'] ?></td>
                <td><?= date('d.m.Y H:i', strtotime($request['date_start'])) ?></td>
                <td><?= date('d.m.Y H:i', strtotime($request['date_finish'])) ?></td>
                <td><?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="edit_request.php?id=<?= $request['id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $request['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Удалить заявку?')">Удалить</button>
                        </form>
                    <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>