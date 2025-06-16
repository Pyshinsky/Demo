<?php
session_start();
include('Config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Получение данных для выпадающих списков
$autos = $pdo->query("SELECT * FROM index_auto")->fetchAll();
$cities = $pdo->query("SELECT * FROM index_city")->fetchAll();

// Создание заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO requests (auto, city_from, city_to, date_start, date_finish, created_by) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['auto'],
        $_POST['city_from'],
        $_POST['city_to'],
        $_POST['date_start'],
        $_POST['date_finish'],
        $_SESSION['user_id']
    ]);
    header("Location: requests.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Новая заявка</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Создание заявки</h2>
    <form method="POST" class="card p-4">
        <div class="mb-3">
            <label class="form-label">Транспорт:</label>
            <select name="auto" class="form-select" required>
                <?php foreach ($autos as $auto): ?>
                    <option value="<?= $auto['id'] ?>"><?= $auto['auto_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Город отправления:</label>
            <select name="city_from" class="form-select" required>
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city['id_city'] ?>"><?= $city['name_city'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Город назначения:</label>
            <select name="city_to" class="form-select" required>
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city['id_city'] ?>"><?= $city['name_city'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Дата отправки:</label>
                <input type="datetime-local" name="date_start" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Дата прибытия:</label>
                <input type="datetime-local" name="date_finish" class="form-control" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Создать</button>
        <a href="requests.php" class="btn btn-secondary">Отмена</a>
    </form>
</body>
</html>