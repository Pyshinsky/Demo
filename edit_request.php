<?php
session_start();
include('Config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Проверка прав доступа
$request_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch();

if (!$request) {
    header("Location: requests.php");
    exit;
}

// Проверка владельца (для обычных пользователей)
if ($_SESSION['role'] !== 'admin' && $request['created_by'] !== $_SESSION['user_id']) {
    header("Location: requests.php");
    exit;
}

// Обновление заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE requests SET 
        auto = ?, city_from = ?, city_to = ?, 
        date_start = ?, date_finish = ?
        WHERE id = ?");
    
    $stmt->execute([
        $_POST['auto'],
        $_POST['city_from'],
        $_POST['city_to'],
        $_POST['date_start'],
        $_POST['date_finish'],
        $request_id
    ]);
    header("Location: requests.php");
    exit;
}

// Получение данных для выпадающих списков
$autos = $pdo->query("SELECT * FROM index_auto")->fetchAll();
$cities = $pdo->query("SELECT * FROM index_city")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактирование заявки</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Редактирование заявки #<?= $request_id ?></h2>
    <form method="POST" class="card p-4">
        <!-- Аналогично create_request.php, но с заполненными значениями -->
        <div class="mb-3">
            <label>Транспорт:</label>
            <select name="auto" class="form-select" required>
                <?php foreach ($autos as $auto): ?>
                    <option value="<?= $auto['id'] ?>" 
                        <?= $auto['id'] == $request['auto'] ? 'selected' : '' ?>>
                        <?= $auto['auto_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Аналогичные блоки для city_from и city_to -->
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Дата отправки:</label>
                <input type="datetime-local" name="date_start" class="form-control" 
                    value="<?= date('Y-m-d\TH:i', strtotime($request['date_start'])) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Дата прибытия:</label>
                <input type="datetime-local" name="date_finish" class="form-control" 
                    value="<?= date('Y-m-d\TH:i', strtotime($request['date_finish'])) ?>" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Обновить</button>
        <a href="requests.php" class="btn btn-secondary">Отмена</a>
    </form>
</body>
</html>