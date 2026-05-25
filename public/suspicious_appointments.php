<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

include '../includes/header.php';

$stmt = $pdo->query("
    SELECT 
        u.id AS patient_id,
        u.name AS patient_name,
        u.email,
        COUNT(a.id) AS total_problem_appointments,
        SUM(a.status = 'cancelled') AS cancelled_count,
        SUM(a.status = 'no-show') AS no_show_count
    FROM users u
    JOIN appointments a ON u.id = a.patient_id
    WHERE u.role = 'patient'
      AND a.status IN ('cancelled', 'no-show')
    GROUP BY u.id, u.name, u.email
    HAVING cancelled_count >= 3 OR no_show_count >= 2
    ORDER BY total_problem_appointments DESC
");

$suspiciousPatients = $stmt->fetchAll();
?>

<h2>كشف المواعيد المشبوهة</h2>

<p class="text-muted">
هذه الصفحة تعرض المرضى الذين لديهم عدد كبير من المواعيد الملغاة أو حالات عدم الحضور.
</p>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>رقم المريض</th>
            <th>اسم المريض</th>
            <th>البريد الإلكتروني</th>
            <th>عدد المواعيد الملغاة</th>
            <th>عدد عدم الحضور</th>
            <th>التنبيه</th>
        </tr>
    </thead>

    <tbody>
        <?php if ($suspiciousPatients): ?>
            <?php foreach ($suspiciousPatients as $patient): ?>
                <tr>
                    <td><?= htmlspecialchars($patient['patient_id']); ?></td>
                    <td><?= htmlspecialchars($patient['patient_name']); ?></td>
                    <td><?= htmlspecialchars($patient['email']); ?></td>
                    <td><?= htmlspecialchars($patient['cancelled_count']); ?></td>
                    <td><?= htmlspecialchars($patient['no_show_count']); ?></td>
                    <td>
                        <span class="badge bg-danger">
                            مريض يحتاج متابعة
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">
                    لا توجد مواعيد مشبوهة حاليًا.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>