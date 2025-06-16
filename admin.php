<?php
session_start();
include('Config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Создание пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    $stmt->execute([$_POST['new_username'], $_POST['new_password']]);
}

// Получение списка пользователей
$users = $pdo->query("SELECT * FROM users WHERE role = 'user' AND active = 1")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Панель администратора</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Администратор: <?= $_SESSION['username'] ?></h2>
    <a href="logout.php">Выйти</a> | <a href="requests.php">Заявки</a>

    <h3 class="mt-4">Добавить логиста</h3>
    <form method="POST" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="new_username" class="form-control" placeholder="Логин" required>
            </div>
            <div class="col-md-4">
                <input type="password" name="new_password" class="form-control" placeholder="Пароль" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="add_user" class="btn btn-success">Добавить</button>
            </div>
        </div>
    </form>

    <h3>Активные логисты</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr><th>ID</th><th>Логин</th><th>Действия</th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['username'] ?></td>
                <td>
                    <form method="POST" action="block_user.php">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Заблокировать</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>