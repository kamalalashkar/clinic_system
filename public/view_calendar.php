<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

// إلغاء الموعد للمريض
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];

    if ($role === 'patient') {
        $stmt = $pdo->prepare("
            UPDATE appointments
            SET status = 'cancelled'
            WHERE id = ? 
              AND patient_id = ?
              AND status != 'cancelled'
        ");
        $stmt->execute([$appointment_id, $user_id]);
    }

    header("Location: view_calendar.php");
    exit();
}

include '../includes/header.php';

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
            echo '<strong>الحالة:</strong> ' . htmlspecialchars($appointment['status']) . '<br>';

            if ($appointment['status'] !== 'cancelled') {
                echo '
                    <form method="POST" class="mt-2" onsubmit="return confirm(\'هل أنت متأكد من إلغاء الموعد؟\');">
                        <input type="hidden" name="appointment_id" value="' . htmlspecialchars($appointment['id']) . '">
                        <button type="submit" name="cancel_appointment" class="btn btn-danger btn-sm">
                            إلغاء الموعد
                        </button>
                    </form>
                ';
            } else {
                echo '<span class="badge bg-danger mt-2">تم إلغاء الموعد</span>';
            }

            echo '</li>';
        }

        echo '</ul>';
    } else {
        echo "<p>لا توجد مواعيد حالياً.</p>";
    }

} elseif ($role === 'doctor') {
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