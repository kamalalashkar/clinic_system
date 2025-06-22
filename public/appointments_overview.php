<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

include '../includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id'], $_POST['status'])) {
    $id = intval($_POST['appointment_id']);
    $status = $_POST['status'];

    $allowed_statuses = ['pending', 'confirmed', 'cancelled', 'completed', 'no-show'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
}

$stmt = $pdo->query("
    SELECT a.*, u1.name AS patient_name, u2.name AS doctor_name
    FROM appointments a
    JOIN users u1 ON a.patient_id = u1.id
    JOIN users u2 ON a.doctor_id = u2.id
    ORDER BY appointment_date DESC
");
$appointments = $stmt->fetchAll();
?>

<h2>عرض المواعيد</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>الرقم</th>
            <th>المريض</th>
            <th>الطبيب</th>
            <th>التاريخ</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($appointments): ?>
            <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><?= $a['id']; ?></td>
                    <td><?= htmlspecialchars($a['patient_name']); ?></td>
                    <td><?= htmlspecialchars($a['doctor_name']); ?></td>
                    <td><?= $a['appointment_date']; ?></td>
                    <td>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="appointment_id" value="<?= $a['id']; ?>">
                            <select name="status" class="form-select form-select-sm">
                                <?php
                                $statuses = ['pending', 'confirmed', 'cancelled', 'completed', 'no-show'];
                                foreach ($statuses as $status) {
                                    $selected = ($status === $a['status']) ? 'selected' : '';
                                    echo "<option value=\"$status\" $selected>$status</option>";
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">تحديث</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">لا توجد مواعيد حتى الآن.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
