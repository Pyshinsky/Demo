<?php
session_destroy();
header('Location: index.php');
exit();

manager.php:
<?php
session_start();
include('Config.php');

// Проверка прав доступа
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'department_head') {
    header("Location: index.php");
    exit;
}

// Обработка POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Добавление нового пользователя
    if (isset($_POST['add_user'])) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['new_username'], $_POST['new_password'], $_POST['new_role']]);
    }

    // Увольнение пользователя
    if (isset($_POST['fire_user'])) {
        $stmt = $pdo->prepare("UPDATE users SET active = 0 WHERE id = ?");
        $stmt->execute([$_POST['user_id']]);
    }

    // Создание смены
    if (isset($_POST['create_shift'])) {
        $stmt = $pdo->prepare("INSERT INTO shifts (name, shift_time) VALUES (?, ?)");
        $stmt->execute([$_POST['shift_name'], $_POST['shift_time']]);
    }

    // Назначение смен
    if (isset($_POST['assign_shift'])) {
        $stmt = $pdo->prepare("INSERT INTO shift_assignments (user_id, shift_id) VALUES (?, ?)");
        $stmt->execute([$_POST['user_id'], $_POST['shift_id']]);
    }
}

// Получение данных
$users = $pdo->query("SELECT * FROM users WHERE active = 1")->fetchAll();
$shifts = $pdo->query("SELECT * FROM shifts")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Заведующий подразделением</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Добро пожаловать, <?php echo $_SESSION['username']; ?> (заведующий)</h2>
    <a href="logout.php">Выйти</a>

    <h3>Добавить нового сотрудника</h3>
    <form method="POST">
        Логин: <input type="text" name="new_username" required>
        Пароль: <input type="password" name="new_password" required>
        Роль:
        <select name="new_role">
            <option value="organizer">Организатор</option>
            <option value="technician">Техник</option>
        </select>
        <button type="submit" name="add_user">Добавить</button>
    </form>

    <h3>Сотрудники</h3>
    <table class="table table-bordered">
        <tr><th>ID</th><th>Логин</th><th>Роль</th><th>Уволить</th></tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['username'] ?></td>
                <td><?= $user['role'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="fire_user">Уволить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Добавить смену</h3>
    <form method="POST">
        Название: <input type="text" name="shift_name" required>
        Время: <input type="text" name="shift_time" required>
        <button type="submit" name="create_shift">Создать</button>
    </form>

    <h3>Назначить смену сотруднику</h3>
    <form method="POST">
        Сотрудник:
        <select name="user_id">
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= $user['username'] ?> (<?= $user['role'] ?>)</option>
            <?php endforeach; ?>
        </select>
        Смена:
        <select name="shift_id">
            <?php foreach ($shifts as $shift): ?>
                <option value="<?= $shift['id'] ?>"><?= $shift['name'] ?> (<?= $shift['shift_time'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="assign_shift">Назначить</button>
    </form>
</body>
</html>
