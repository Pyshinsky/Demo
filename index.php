<?php
session_start();
include('Config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password AND active = 1");
    $stmt->execute(['username' => $username, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Редирект в зависимости от роли
        switch ($user['role']) {
            case 'department_head':
                header("Location: manager.php");
                break;
            case 'organizer':
                header("Location: organizer.php");
                break;
            case 'technician':
                header("Location: technician.php");
                break;
            default:
                $error = "Неизвестная роль пользователя.";
        }
        exit;
    } else {
        $error = "Неверный логин или пароль или пользователь уволен!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход в систему конференций</title>
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
