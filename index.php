<?php
session_start();
include('Config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Поиск пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Проверка пароля и активности
        if ($user['password'] === $password && $user['active'] == 1) {
            // Сброс счетчика попыток
            $resetStmt = $pdo->prepare("UPDATE users SET login_attempts = 0 WHERE id = ?");
            $resetStmt->execute([$user['id']]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Редирект по ролям
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: requests.php");
            }
            exit;
        } else {
            // Увеличение счетчика попыток
            $newAttempts = $user['login_attempts'] + 1;
            $updateStmt = $pdo->prepare("UPDATE users SET login_attempts = ? WHERE id = ?");
            $updateStmt->execute([$newAttempts, $user['id']]);

            // Блокировка после 3 попыток (кроме админа)
            if ($newAttempts >= 3 && $user['role'] !== 'admin') {
                $blockStmt = $pdo->prepare("UPDATE users SET active = 0 WHERE id = ?");
                $blockStmt->execute([$user['id']]);
                $error = "Аккаунт заблокирован. Обратитесь к администратору.";
            } else {
                $error = "Неверный логин или пароль. Осталось попыток: " . (3 - $newAttempts);
            }
        }
    } else {
        $error = "Пользователь не найден";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход в систему логистики</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Авторизация</h2>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST" class="card p-4">
        <div class="mb-3">
            <label class="form-label">Логин:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Пароль:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
</body>
</html>