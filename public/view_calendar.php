<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include '../includes/header.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

if ($role === 'patient') {
    $stmt = $pdo->prepare("
        SELECT a.*, u.name AS doctor_name
        FROM appointments a
        JOIN users u ON a.doctor_id = u.id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date
    ");
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll();

    echo "<h2>مواعيدي</h2>";

    if (count($appointments) > 0) {
        echo '<ul class="list-group">';
        foreach ($appointments as $appointment) {
            echo '<li class="list-group-item">';
            echo '<strong>الطبيب:</strong> د. ' . htmlspecialchars($appointment['doctor_name']) . '<br>';
            echo '<strong>التاريخ:</strong> ' . htmlspecialchars($appointment['appointment_date']) . '<br>';
            echo '<strong>الحالة:</strong> ' . htmlspecialchars($appointment['status']);
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo "<p>لا توجد مواعيد حالياً.</p>";
    }

} elseif ($role === 'doctor') {
    // مواعيد الطبيب مع اسم المريض
    $stmt = $pdo->prepare("
        SELECT a.*, u.name AS patient_name
        FROM appointments a
        JOIN users u ON a.patient_id = u.id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date
    ");
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll();

    echo "<h2>جميع مواعيدي</h2>";

    if (count($appointments) > 0) {
        echo '<ul class="list-group">';
        foreach ($appointments as $appointment) {
            echo '<li class="list-group-item">';
            echo '<strong>المريض:</strong> ' . htmlspecialchars($appointment['patient_name']) . '<br>';
            echo '<strong>التاريخ:</strong> ' . htmlspecialchars($appointment['appointment_date']) . '<br>';
            echo '<strong>الحالة:</strong> ' . htmlspecialchars($appointment['status']);
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo "<p>لا توجد مواعيد حالياً.</p>";
    }

} else {
    echo "<p>لا يحق لك عرض المواعيد هنا.</p>";
}

include '../includes/footer.php';
?>
