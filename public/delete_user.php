<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';
checkAdmin();
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id == $_SESSION['user']['id']) {
        die("لا يمكنك حذف حسابك الشخصي!");
    }
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $role = $stmt->fetchColumn();
    if ($role === 'doctor') {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE doctor_id = ?");
        $stmt->execute([$id]);
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    die("معرف المستخدم غير صالح!");
}
