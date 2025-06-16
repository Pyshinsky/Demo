<?php
session_start();
include('Config.php');

// Только для администратора
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE users SET active = 0 WHERE id = ?");
    $stmt->execute([$_POST['user_id']]);
}

header("Location: admin.php");
exit;