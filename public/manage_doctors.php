<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

include '../includes/header.php';

$stmt = $pdo->query("
    SELECT u.*, d.specialty, d.consultation_fee
    FROM users u
    LEFT JOIN doctors d ON u.id = d.user_id
    WHERE u.role = 'doctor'
");
$doctors = $stmt->fetchAll();
?>

<h2>إدارة الأطباء</h2>

<a href="add_doctor.php" class="btn btn-success mb-3">إضافة طبيب جديد</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>الرقم</th>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>التخصص</th>
            <th>أتعاب الكشف</th>
            <th>الإجراءات</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($doctors): ?>
            <?php foreach ($doctors as $d): ?>
                <tr>
                    <td><?= $d['id']; ?></td>
                    <td><?= htmlspecialchars($d['name']); ?></td>
                    <td><?= htmlspecialchars($d['email']); ?></td>
                    <td><?= htmlspecialchars($d['specialty']); ?></td>
                    <td><?= htmlspecialchars($d['consultation_fee']); ?> </td>
                    <td>
                        <a href="edit_user.php?id=<?= $d['id']; ?>" class="btn btn-sm btn-primary">تعديل</a>
                        <a href="delete_user.php?id=<?= $d['id']; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('هل أنت متأكد من حذف هذا الطبيب؟');">حذف</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">لا يوجد أطباء مسجلين حتى الآن.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
