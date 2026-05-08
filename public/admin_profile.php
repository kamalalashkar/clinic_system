<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

include '../includes/header.php';

$admin_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT id, name, email, role, phone, gender, dob FROM users WHERE id = ? AND role = 'admin'");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "<div class='alert alert-danger'>لم يتم العثور على بيانات الإداري.</div>";
    include '../includes/footer.php';
    exit;
}
?>

<h2 class="mb-4">الملف الشخصي للإداري</h2>

<div class="card">
    <div class="card-header bg-primary text-white">
        بيانات الحساب
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>الاسم</th>
                <td><?= htmlspecialchars($admin['name']) ?></td>
            </tr>
            <tr>
                <th>البريد الإلكتروني</th>
                <td><?= htmlspecialchars($admin['email']) ?></td>
            </tr>
            <tr>
                <th>رقم الهاتف</th>
                <td><?= htmlspecialchars($admin['phone'] ?? 'غير محدد') ?></td>
            </tr>
            <tr>
                <th>الجنس</th>
                <td><?= htmlspecialchars($admin['gender'] ?? 'غير محدد') ?></td>
            </tr>
            <tr>
                <th>تاريخ الميلاد</th>
                <td><?= htmlspecialchars($admin['dob'] ?? 'غير محدد') ?></td>
            </tr>
            <tr>
                <th>نوع الحساب</th>
                <td>إداري</td>
            </tr>
        </table>
    </div>
</div>

<a href="admin_edit_profile.php" class="btn btn-primary mt-3">
    تعديل الملف الشخصي
</a>

<a href="admin_dashboard.php" class="btn btn-secondary mt-3">العودة للوحة التحكم</a>


<?php include '../includes/footer.php'; ?>