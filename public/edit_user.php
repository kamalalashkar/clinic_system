<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';
checkAdmin();
if (!isset($_GET['id'])) {
    die("رقم المستخدم غير موجود!");
}
$id = intval($_GET['id']);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $id]);
    header("Location: manage_patients.php");
    exit();
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    die("المستخدم غير موجود!");
}
include '../includes/header.php';
?>
<h2>تعديل بيانات المستخدم</h2>
<form method="POST">
  <div class="mb-3">
    <label class="form-label">الاسم</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">البريد الإلكتروني</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
  </div>
  <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
</form>
<?php include '../includes/footer.php'; ?>
