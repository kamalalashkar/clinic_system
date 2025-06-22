<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

include '../includes/header.php';

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin'");
$admins = $stmt->fetchAll();
?>

<h2>إدارة الإداريين</h2>

<a href="add_admin.php" class="btn btn-success mb-3">إضافة إداري جديد</a>

<!-- جدول الإداريين -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>الرقم</th>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>الإجراءات</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($admins): ?>
            <?php foreach ($admins as $a): ?>
                <tr>
                    <td><?= $a['id']; ?></td>
                    <td><?= htmlspecialchars($a['name']); ?></td>
                    <td><?= htmlspecialchars($a['email']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $a['id']; ?>" class="btn btn-sm btn-primary">تعديل</a>
                        <a href="delete_user.php?id=<?= $a['id']; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('هل أنت متأكد من حذف هذا الإداري؟');">حذف</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">لا يوجد إداريين مسجلين حتى الآن.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
