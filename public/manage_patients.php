<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';
checkAdmin();
include '../includes/header.php';
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'patient'");
$patients = $stmt->fetchAll();
?>
<h2>إدارة المرضى</h2>
<table class="table table-bordered"><thead><tr><th>الرقم</th><th>الاسم</th><th>البريد الإلكتروني</th><th>الإجراءات</th></tr></thead><tbody>
<?php foreach ($patients as $p): ?>
<tr><td><?= $p['id']; ?></td><td><?= htmlspecialchars($p['name']); ?></td><td><?= htmlspecialchars($p['email']); ?></td>
<td>
  <a href="edit_user.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-primary">تعديل</a>
  <a href="delete_user.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-danger"
     onclick="return confirm('هل أنت متأكد من الحذف؟');">حذف</a>
</td></tr>
<?php endforeach; ?></tbody></table><?php include '../includes/footer.php'; ?>
